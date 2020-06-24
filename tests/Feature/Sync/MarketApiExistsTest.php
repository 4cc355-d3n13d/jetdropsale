<?php

namespace Tests\Feature\Sync;

use App\Models\Shopify\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\ArrangeThings;
use Tests\Traits\UsesSqlite;

class MarketApiExistsTest extends TestCase
{
    private const SHOP = 'dev-dropwow.myshopify.com';

    use DatabaseMigrations;
    use ArrangeThings;
    use UsesSqlite;

    private const SOURCE_REFERENCE = 'src_1D9EaTIJb8S3vLIy3zMX0I4y';
    private const CUSTOMER_REFERENCE = 'cus_DaPPcfkr32xm3T';


    public function testCSCartShopResponse(): void
    {
        $this->arrangeUserShop($this::SHOP, false);
        $shop = Shop::lastOrFail();
        $this->assertTrue($shop->isExistsInCSCart());
    }
}
