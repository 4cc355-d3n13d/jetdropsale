<?php

namespace Tests\Feature\Sync;

use App\Enums\MyProductStatusType;
use App\Jobs\MarketSync\ImportProductFromCSCart;
use App\Models\Product\Product;
use App\Models\User;
use App\Services\CSCartClientService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Tests\Traits;

class ImportProductsTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;


    public function testImportJobDispatched(): void
    {
        Bus::fake();
        $this->arrangeUserShop();

        $client = $this->createMock(CSCartClientService::class);
        $client->method('get')->willReturn($this->fakeTaxi());
        app()->instance(CSCartClientService::class, $client);

        Auth::loginUsingId(1);
        Bus::assertDispatched(ImportProductFromCSCart::class);
    }

    public function testRealQueueSyncProductsImported(): void
    {
        $client = $this->createMock(CSCartClientService::class);
        $client->method('get')->willReturn($this->fakeTaxi());
        app()->instance(CSCartClientService::class, $client);

        create(Product::class, ['ali_id' => 32824928565]);

        $this->arrangeUserShop();
        Auth::loginUsingId(1);
        create(User::class);

        $this->seeInDatabase('products', [
            'ali_id' => 32824928565,
        ]);

        $this->seeInDatabase('my_products', [
            'status' =>  MyProductStatusType::CONNECTED
        ]);

        $this->seeInDatabase('shopify_products', [
            'shopify_id' =>  1535549734982
        ]);
    }

    public function fakeTaxi()
    {
        return <<<EOD
{
    "result": "ok",
    "status": 200,
    "data": {
        "user": [{
            "email": "mail@dropwow.com",
            "user_id": 13265,
            "balance": null
        }],
        "products": {
            "1535549734982": 32824928565,
            "1535550062662": 32853915487,
            "1535550750790": 32858751563,
            "1535551242310": 1000005682497,
            "1535551897670": 32875368675,
            "1619186024518": 32823445909,
            "1619267649606": 32888446027,
            "1619361235014": 32705612968,
            "1622864363590": 32822104099,
            "1632210780230": 32843467816
        }
    }
}
EOD;
    }
}
