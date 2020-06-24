<?php

namespace Tests\Feature\Sync;

use App\Enums\OrderOriginType;
use App\Models\Product\Product;
use App\Services\CSCartClientService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits;

class ImportOrdersTest extends TestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;


    public function testOrderImport(): void
    {
        // Create the stub.
        $CSCartApiClientMigrationServiceDropwowStub = $this->createMock(CSCartClientService::class);
        // Configure the stub.
        $CSCartApiClientMigrationServiceDropwowStub->method('get')->willReturn($this->fakeTaxi());
        // Bind the stub to Container
        app()->instance(CSCartClientService::class, $CSCartApiClientMigrationServiceDropwowStub);
        // Create dummy product
        create(Product::class, ['ali_id' => 32774533361]);
        // Dummy shop
        $this->arrangeUserShop();

        $this->seeInDatabase('orders', [
            'origin' => OrderOriginType::SHOPIFY,
            'origin_id' => 6200324295,
        ]);
    }

    private function fakeTaxi(): string
    {
        return <<<JSON
{
    "result": "ok",
    "status": 200,
    "data": {
        "user": [
            {
                "email": "jttglobal@gmail.com",
                "user_id": 596,
                "balance": 1.45
            }
        ],
        "orders": [
            {
                "order_id":408,
                "shopify_order_id":6200324295,
                "is_parent_order":"N",
                "parent_order_id":0,
                "vendor_aff_check":"Y",
                "vendor_ordered":"Y",
                "vendor_order_id":504468640616254,
                "company_id":7,
                "issuer_id":596,
                "user_id":596,
                "total":13.86,
                "subtotal":11.96,
                "discount":0,
                "subtotal_discount":0,
                "payment_surcharge":0,
                "shipping_ids":7,
                "shipping_cost":1.9,
                "partner_commission":0,
                "timestamp":1508914448,
                "status":"Completed",
                "vendor_status":"Delivered",
                "vendor_status_date":"2017-11-21 11:10:11",
                "notes":null,
                "details":"couldn't add to cart",
                "promotions":"a:0:{}",
                "promotion_ids":"",
                "firstname":"Jason",
                "lastname":"Morales",
                "company":"",
                "b_firstname":"",
                "b_lastname":"",
                "b_address":"",
                "b_address_2":"",
                "b_city":"",
                "b_county":"",
                "b_state":"",
                "b_country":"US",
                "b_zipcode":"",
                "b_phone":"",
                "s_firstname":"Jason",
                "s_lastname":"Morales",
                "s_address":"3024 34TH st. SW ",
                "s_address_2":"",
                "s_city":"Lehigh Acres",
                "s_county":"",
                "s_state":"FL",
                "s_country":"US",
                "s_zipcode":33976,
                "s_phone":"",
                "s_address_type":"",
                "phone":"",
                "fax":"",
                "url":"",
                "email":"jttglobal@gmail.com",
                "payment_id":0,
                "tax_exempt":"N",
                "lang_code":"en",
                "ip_address":"17e3376e",
                "repaid":0,
                "validation_code":"",
                "localization_id":0,
                "profile_id":0,
                "wallet_refunded_amount":0,
                "pay_by_wallet_amount":13.96,
                "tracking_number":null,
                "ordered_products": [
                    {
                        "product_id": 4158,
                        "ali_product_id": 32774533361,
                        "shopify_product_id": 57303105545,
                        "title": "Electric hair straightener brush Hair Care Styling hair straightener Comb Auto Massager Straightening Irons SimplyFast Hair iron",
                        "amount": 1,
                        "price": 11.96,
                        "modifiers_price": 0,
                        "base_price": 11.96,
                        "display_price": 11.96,
                        "image_path": "https://market.dropwow.com/images/detailed/144/88O1e13jd5YZ8081654v5nRA34r807A5.jpeg",
                        "http_image_path": "http://market.dropwow.com/images/detailed/144/88O1e13jd5YZ8081654v5nRA34r807A5.jpeg",
                        "https_image_path": "https://market.dropwow.com/images/detailed/144/88O1e13jd5YZ8081654v5nRA34r807A5.jpeg",
                        "absolute_path": "/var/www/market/images/detailed/144/88O1e13jd5YZ8081654v5nRA34r807A5.jpeg",
                        "relative_path": "detailed/144/88O1e13jd5YZ8081654v5nRA34r807A5.jpeg",
                        "product_options": {
                            "4429": {
                                "option_id": 4429,
                                "ali_option_id": 14,
                                "shopify_option_id": null
                            }
                        },
                        "product_options_value": [
                            {
                                "option_id": 4429,
                                "variant_id": 20536,
                                "ali_variant_id": 366,
                                "shopify_variant_id": null
                            }
                        ]
                    }
                ]
            }
        ],
        "products": {
            "57303105545": 32774533361
        }
    }
}
JSON;
    }
}
