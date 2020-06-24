<?php

namespace App\Services;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\MyProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class MyProductService
{
    private const DESCRIPTION_LINE_DELIMITER = '<br>';

    private $newDescription;

    /** @var User $user */
    private $user;

    /** @var PriceModifierService $priceModifier*/
    private $priceModifier;

    private $type;
    private $vendor;

    private $priceProduct;
    private $priceVariant;

    public function getMyProduct(int $id): MyProduct
    {
        return MyProduct::with(['options', 'combinations'])->findOrFail($id);
    }

    public function split(MyProduct $myProduct, Authenticatable $user, int $option_id): Collection
    {
        $this->user = $user;
        $this->newDescription = $this->getNewDescription($myProduct);

        $splittedMyProducts = collect();
        $ali_option_id = MyProductOption::findOrFail($option_id)->ali_option_id;
        $optionsToSplit = $this->prepareOptionsListByAliId($myProduct, $ali_option_id, '=');
        $optionsToClone = $this->prepareOptionsListByAliId($myProduct, $ali_option_id, '!=');

        $optionsToSplit->each(function (MyProductOption $optionSplit) use ($myProduct, $splittedMyProducts, $optionsToClone) {
            $this->priceProduct = $myProduct->price;

            // New product, new title w/ selected option for split
            /** @var MyProduct $newMyProduct */
            $newMyProduct = $this->fillNewMyProduct($myProduct, $myProduct->product_id);
            $newMyProduct->title = '[' . $optionSplit->name . ':' . $optionSplit->value . '] ' . $newMyProduct->title;
            $newMyProduct->image = $optionSplit->image ?? $myProduct->image;

            // Clone the rest options
            $optionsToClone->each(function (MyProductOption $optionClone) use ($newMyProduct) {
                $this->fillNewMyProductOptions($optionClone, $newMyProduct);
            });

            // Clone only that vars, wich combinations has our value
            $combinationsToClone = $this->prepareVariantsList($myProduct, $optionSplit->ali_sku, $optionSplit->ali_option_id);
            $combinationsToClone->each(function (MyProductVariant $variantClone) use ($newMyProduct) {
                $this->priceVariant = $variantClone->price;

                return $this->fillNewMyProductVariants($variantClone, $newMyProduct);
            });

            // Clone tags
            $myProduct->tags->map(function (MyProductTag $tag) use ($newMyProduct) {
                return $this->fillNewMyProductTags($tag, $newMyProduct);
            });

            //TODO: wait collections
            //clone collections
            //$myProduct->collections->map(function (MyProductCollection $collection) use ($newMyProduct) {
            //    return $newMyProduct->collections()->save($collection);
            //});

            $newMyProduct->save();

            return $splittedMyProducts->push($newMyProduct);
        });

        return $splittedMyProducts;
    }

    private function prepareOptionsListByAliId(MyProduct $myProduct, int $ali_option_id, string $operand)
    {
        $myProductOptionsValues = $myProduct->options->where('ali_option_id', $operand, $ali_option_id)->pluck('value', 'id')->toArray();

        $countOptionsValues = $myProductOptionsValues ? array_count_values($myProductOptionsValues) : [];

        $optionsList = $myProduct->options->map(function (MyProductOption $option) use ($countOptionsValues, $myProductOptionsValues) {
            if (array_key_exists($option->id, $myProductOptionsValues)) {
                $option->value = ($countOptionsValues && $countOptionsValues[$option->value] > 1) ?
                    $option->value . ' - ' . $option->id :
                    $option->value;

                return $option;
            }

            return null;
        })->filter(function ($value, $key) {
            return $value != null;
        });

        return $optionsList;
    }

    private function prepareVariantsList(MyProduct $myProduct, int $option_sku, int $ali_option_id)
    {
        return $myProduct->combinations()->whereJsonContains('combination', [$ali_option_id => $option_sku])->getResults();
    }

    public function clone($anyProduct, User $user): MyProduct
    {
        $this->user = $user;
        $this->priceModifier = new PriceModifierService($this->user);
        $this->newDescription = $this->getNewDescription($anyProduct);

        $clonedMyProduct = null;

        if ($anyProduct instanceof Product) {
            $clonedMyProduct = $this->cloneFromProduct($anyProduct);
        } elseif ($anyProduct instanceof MyProduct) {
            $clonedMyProduct = $this->cloneFromMyProduct($anyProduct);
        }

        return $clonedMyProduct;
    }

    private function getNewDescription($anyProduct): string
    {
        $this->newDescription = implode(self::DESCRIPTION_LINE_DELIMITER, $anyProduct->details->map(function (ProductDetail $detail) {
            return rtrim($detail->title, ':') . ': ' . $detail->value;
        })->toArray());

        return $this->newDescription;
    }

    private function cloneFromProduct(Product $product): MyProduct
    {
        $this->priceProduct = $this->priceModifier->modify($product->price);

        /** @var MyProduct $newMyProduct */
        $newMyProduct = $this->fillNewMyProduct($product, $product->id);

        //clone options
        $product->options->map(function (ProductOption $option) use ($newMyProduct) {
            return $this->fillNewMyProductOptions($option, $newMyProduct);
        });

        //clone variants
        $product->combinations->map(function (ProductVariant $variant) use ($newMyProduct) {
            $this->priceVariant = $this->priceModifier->modify($variant->price);

            return $this->fillNewMyProductVariants($variant, $newMyProduct);
        });

        return $newMyProduct;
    }

    private function cloneFromMyProduct(MyProduct $myProduct): MyProduct
    {
        $this->type = $myProduct->type;
        $this->vendor = $myProduct->vendor;
        $this->priceProduct = $myProduct->price;

        /** @var MyProduct $newMyProduct */
        $newMyProduct = $this->fillNewMyProduct($myProduct, $myProduct->product_id);

        //clone options
        $myProduct->options->map(function (MyProductOption $option) use ($newMyProduct) {
            return $this->fillNewMyProductOptions($option, $newMyProduct);
        });

        //clone variants
        $myProduct->combinations->map(function (MyProductVariant $variant) use ($newMyProduct) {
            $this->priceVariant = $variant->price;

            return $this->fillNewMyProductVariants($variant, $newMyProduct);
        });

        //clone tags
        $myProduct->tags->map(function (MyProductTag $tag) use ($newMyProduct) {
            return $this->fillNewMyProductTags($tag, $newMyProduct);
        });

        //clone collections
        $myProduct->collections->map(function (MyProductCollection $collection) use ($newMyProduct) {
            return $newMyProduct->collections()->save($collection);
        });

        return $newMyProduct;
    }

    private function fillNewMyProduct($anyProduct, $productId): MyProduct
    {
        /** @var MyProduct $newMyProduct */
        $newMyProduct = new MyProduct();
        $newMyProduct->fill([
            'title' => $anyProduct->title,
            'status' => MyProductStatusType::NON_CONNECTED,
            'price' => $this->priceProduct,
            'amount' => $anyProduct->amount,
            'description' => $this->newDescription,
            'image' => $anyProduct->image,
            'images' => $anyProduct->images,
            'ali_id' => $anyProduct->ali_id,
            'product_id' => $productId,
            'type' => $this->type,
            'vendor' => $this->vendor,
        ]);

        $newMyProduct->user()->associate($this->user);
        $newMyProduct->save();

        return $newMyProduct;
    }

    private function fillNewMyProductOptions($anyOption, MyProduct $newMyProduct): MyProductOption
    {
        $newMyProductOption = new MyProductOption();
        $newMyProductOption->fill([
            'name' => $anyOption->name,
            'value' => $anyOption->value,
            'image' => $anyOption->image,
            'ali_sku' => $anyOption->ali_sku,
            'ali_option_id' => $anyOption->ali_option_id,
            'my_product_id' => $newMyProduct->id,
        ]);
        $newMyProductOption->save();

        return $newMyProductOption;
    }

    private function fillNewMyProductVariants($anyVariant, MyProduct $newMyProduct): MyProductVariant
    {
        $newMyProductVariant = new MyProductVariant();
        $newMyProductVariant->fill([
            'sku' => $anyVariant->sku,
            'amount' => $anyVariant->amount,
            'price' => $this->priceVariant,
            'combination' => $anyVariant->combination,
            'product_variant_id' => $anyVariant->id
        ]);

        $newMyProductVariant->myProduct()->associate($newMyProduct);
        $newMyProductVariant->save();

        return $newMyProductVariant;
    }

    private function fillNewMyProductTags($anyTag, MyProduct $newMyProduct): MyProductTag
    {
        $newMyProductTag = new MyProductTag();
        $newMyProductTag->fill([
            'title' => $anyTag->title,
            'user_id' => $anyTag->user_id,
            'my_product_id' => $newMyProduct->id,
        ]);
        $newMyProductTag->myProduct()->associate($newMyProduct);
        $newMyProductTag->save();

        return $newMyProductTag;
    }
}
