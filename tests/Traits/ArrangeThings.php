<?php

namespace Tests\Traits;

use App\Enums\OrderOriginType;
use App\Enums\ShopifyStatusType;
use App\Models\Card;
use App\Models\Order;
use App\Models\OrderCart;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\MyProductVariant;
use App\Models\Product\Product;
use App\Models\Product\ProductOption;
use App\Models\Product\ProductVariant;
use App\Models\Shopify;
use App\Models\Shopify\ShopifyProduct;
use App\Models\User;

trait ArrangeThings
{
    // Creating the user with stripe references (+card) and a shop. User is ready to work
    public function arrangeUserShop(string $shopDomain = 'test_shop.myshopify.com', bool $payable = true): self
    {
        // 1. Creating the shop
        if (! $shop = Shopify\Shop::where(['shop' => $shopDomain])->first()) {
            /** @var Shopify\Shop $shop */
            $shop = create(Shopify\Shop::class, ['shop' => $shopDomain]);
        }

        $payable && $this->makeUserPayable($shop->user);

        return $this;
    }

    public function makeUserPayable(User $user): void
    {
        ! env('STRIPE_TEST_SOURCE_REFERENCE') && fwrite(STDERR, "[*] WARNING: The env STRIPE_TEST_SOURCE_REFERENCE constant is not defined! User '{$user->email}' won`t be payable.\n");
        ! env('STRIPE_TEST_CUSTOMER_REFERENCE') && fwrite(STDERR, "[*] WARNING: The env STRIPE_TEST_CUSTOMER_REFERENCE constant is not defined! User '{$user->email}' won`t be payable.\n");
        env('STRIPE_TEST_CUSTOMER_REFERENCE') && $user->update(['billing_reference' => env('STRIPE_TEST_CUSTOMER_REFERENCE')]);

        create(Card::class, [
            'user_id' => $user->id,
            'primary' => true,
            'billing_reference' => env('STRIPE_TEST_SOURCE_REFERENCE'),
        ]);
    }

    public function arrangeUserProducts(int $goodsCount = 8): self
    {
        create(Product::class, [], $goodsCount)->each(function (Product $product) {
            $product->options()->save(make(ProductOption::class));
            $product->combinations()->save(make(ProductVariant::class));
            $product->options()->save(make(ProductOption::class));
        });

        return $this;
    }

    /**
     * Инициализируем конкретные гуды (товары) с конкретными параметрами
     * (возможно, лучше добавить такую возможность arrangeSingleProductOrder ...)
     * @param Shopify\Shop|int|null $shop
     */
    public function arrangeShopGoods($shop = null, int $goodsCount = 8): self
    {
        $shop = $shop instanceof Shopify\Shop
            ? $shop
            : (is_int($shop) ? Shopify\Shop::findOrFail($shop) : Shopify\Shop::lastOrFail())
        ;

        // 2. Creating the product
        create(MyProduct::class, [
            'user_id' => $shop->user_id
        ], $goodsCount)->each(function (MyProduct $product) use ($shop) {
            $product->options()->save(make(MyProductOption::class));
            $product->combinations()->save(make(MyProductVariant::class));
            $product->options()->save(make(MyProductOption::class));
            $product->tags()->save(make(MyProductTag::class, ['user_id' => $shop->user_id, 'my_product_id' => $product->product_id]));
            $product->collections()->save(make(MyProductCollection::class, ['user_id' => $shop->user_id]));
        });

        // 3. Create with the variant
        $myProductVariant = create(MyProductVariant::class, ['price' => 3.2, 'amount' => 2]);

        create(ShopifyProduct::class, [
            'shopify_id' => 1,
            'user_id' => $shop->user_id,
            'my_product_id' => $myProductVariant->myProduct->id,
            'product_id' => $myProductVariant->myProduct->product_id
        ]);

        create(Shopify\ProductVariant::class, [
            'shopify_variant_id' => 2,
            'product_id' => $myProductVariant->myProduct->id,
            'product_variant_id' => $myProductVariant->id
        ]);

        return $this;
    }

    // Dummy
    protected function arrangeEmptyOrder(Shopify\Shop $shop): Order
    {
        return create(Order::class, [
            'shop_id'          => $shop->id,
            'user_id'          => $shop->user_id,
            'origin'           => OrderOriginType::SHOPIFY,
            'origin_id'        => mt_rand(1, 999999),
            'origin_status'    => ShopifyStatusType::PARTIALLY_PAID,
            'product_variants' => [],
        ]);
    }

    // Synthetics
    protected function arrangeOrderWithProducts(Shopify\Shop $shop, $count = 1): Order
    {
        $order = $this->arrangeEmptyOrder($shop);
        for ($i = 0; $i <= $count; $i++) {
            create(OrderCart::class, ['order_id' => $order->id]);
        }

        return $order;
    }

    // Synthetics
    protected function arrangeSingleProductOrder(Shopify\Shop $shop, float $price = 20): Order
    {
        $order = $this->arrangeEmptyOrder($shop);
        create(OrderCart::class, ['order_id' => $order->id, 'price' => $price]);

        return $order;
    }

    // Throw a natural order
    protected function sendShopifyOrder(string $shopDomain = 'test_shop.myshopify.com'): self
    {
        $this->withExceptionHandling();

        return $this->postJson('/test/shopify/orders/create', $this->mockJson('ShopifyOrder.txt'), [
            'x-shopify-shop-domain' => $shopDomain,
        ]);
    }
}
