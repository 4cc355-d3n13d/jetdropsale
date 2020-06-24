<?php

namespace App\Jobs;

use App\Models\Product\Product;
use App\Models\Product\Category;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductDetail;
use App\Models\Product\ProductVariant;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ImportAliProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private const ALI_URL_REGEXP = '#^(https?:)?//(www\.)?aliexpress.com/category/(?<id>\d+)/(?<slug>[\w\-]+)\.html$#';

    /** @var array */
    public $productData;

    public function __construct(array $productData)
    {
        $this->productData = $productData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Create or update categories
        $parentCategoryId = 0;
        $categoriesPathArray = [];
        if (! empty($this->productData['categories']) && ! empty($this->productData['categoriesUrl'])) {
            $iterator = new \MultipleIterator();
            $iterator->attachIterator(new \ArrayIterator($this->productData['categories']));
            $iterator->attachIterator(new \ArrayIterator($this->productData['categoriesUrl']));

            foreach ($iterator as $level => $category) {
                ! $level && $parentCategoryId = 0;

                list($categoryName, $categoryUrl) = $category;
                preg_match(self::ALI_URL_REGEXP, $categoryUrl, $categoryData);

                $category = Category::firstOrNew(['ali_id' => $categoryData['id']]);
                $category->slug = $categoryData['slug'];
                $category->title = $category->ali_title = $categoryName;
                $category->parent_id = $parentCategoryId;
                $category->save();

                $parentCategoryId = $category->id;
                $categoriesPathArray[] = $category->id;
            }
        }
        // If the product has any category we do not need it
        if (!$parentCategoryId) {
            return;
        }

        // Create (or update) the product
        $product = Product::firstOrNew(['ali_id' => $this->productData['product_code']]);
        $product->price = $this->productData['price'] * config('settings.product.markup', 1);
        $product->amount = $this->productData['amount'];

        // If we had the product - only update the price and quantity
        if (!$product->id) {
            $product->ali_id = $this->productData['product_code'];
            $product->category_id = $parentCategoryId;
            $product->categoriesPath = implode('/', $categoriesPathArray);
            $product->image = $this->productData['mainImageUrl'];
            $product->images = json_encode($this->productData['addImageUrls']);
            $product->title = $this->productData['product'];
            $product->description = $this->productData['description'];
            if (!$product->save()) {
                Log::error('не удалось сохранить продукт');
                return;
            }
            if (config('app.env') == 'production') {
                $product->image = $product->imageFromUrl($product->image);
                $product->images = json_encode($product->imageFromUrl(json_decode($product->images)));
            }
        }
        $product->save();
        StoreProductDetails::dispatch($this->productData['details'], $product->id);

        // Create all possible variants of the product
        if ($this->productData['options']) {
            foreach ($this->productData['options'] as $option) {
                $option = (array) $option;
                foreach ($option['variants'] as $variant) {
                    $variant = (array) $variant;
                    $productOption = ProductOption::firstOrCreate([
                        'product_id' => $product->id,
                        'name' => $option['option_name'],
                        'value' => $variant['variant_name'],
                        'image' => $variant['image_path'],
                        'ali_option_id' => $option['ali_option_id'],
                        'ali_sku' => $variant['ali_variant_id'],
                    ]);

                    if (! $productOption) {
                        Log::error('Не удалось создать опции продукта', ['product' => $product]);
                        try {
                            $product->delete();
                        } catch (\Exception $e) {
                        }

                        Log::error('Не удалось сохранить комбинации');
                        return;
                    }
                }
            }
        }

        // Combinations of all options and quantities
        // list of all touched combination ids
        $insert = [];
        if ($this->productData['combinations']) {
            foreach ($this->productData['combinations'] as $combination) {
                $combination = (array) $combination;
                $combinationIds = (array) $combination['combination'];
                ksort($combinationIds);

                $insert[] = [
                    'product_id' => $product->id,
                    'sku' => $product->skuGenerate($combinationIds),
                    'amount' => $combination['amount'],
                    'price' => $combination['price'] * config('settings.product.markup', 1),
                    'combination' => json_encode($combinationIds),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
        }

        if ($insert) {
            $insertUpdateResult = ProductVariant::insertOnDuplicateKey($insert, ['price', 'amount', 'updated_at']);
            Log::info('Вставили записи, результат: ' . $insertUpdateResult);
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Exception $exception): void
    {
        // Send user notification of failure, etc...
    }
}
