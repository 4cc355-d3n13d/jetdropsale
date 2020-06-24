<?php

namespace Tests\Feature\Api\Internal;

use App\Models\Shopify\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\ApiTestCase;
use Tests\Traits;

class OrderTest extends ApiTestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;


    public function testOrdersList(): void
    {
        $this->arrangeUserShop();

        $shop = Shop::lastOrFail();

        $this->arrangeSingleProductOrder($shop);

        Auth::loginUsingId($shop->user->id);

        $this->getJson('/api/user/orders')->seeStatusCode(200);

        $this->assertCount(1, $this->response->getData()->orders);
    }
}
