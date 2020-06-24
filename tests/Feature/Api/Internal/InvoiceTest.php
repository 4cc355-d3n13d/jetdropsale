<?php

namespace Tests\Feature\Api\Internal;

use App\Models\Order;
use App\Models\Shopify;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Tests\ApiTestCase;
use Tests\Traits;

class InvoiceTest extends ApiTestCase
{
    use DatabaseMigrations;
    use Traits\ArrangeThings;
    use Traits\UsesSqlite;

    private const SOURCE_REFERENCE = 'src_1D9EaTIJb8S3vLIy3zMX0I4y';
    private const CUSTOMER_REFERENCE = 'cus_DaPPcfkr32xm3T';

    public function testSeeAndPayInvoice(): void
    {
        $this->arrangeUserShop();

        $shop = Shopify\Shop::lastOrFail();
        create(Order::class, [
            'user_id'          => $shop->user_id,
            'shop_id'          => $shop->id,
            'origin_id'        => 2,
            'product_variants' => [
                'product_id' => 2,
                'sku' => '1500773252',
                'variant_id' => '2',
                'shopify_variant_id' => '1',
                'price' => 4.56,
                'quantity' => 4
            ]
        ]);

        Auth::loginUsingId(1);

        $this->assertSwaggerRequestResponse('GET', '/api/user/invoices', 200);

        // Would not work. Need fix. See CardTest.php for problem description
        // $this->assertSwaggerRequestResponse('GET', '/api/user/invoices/{invoice_id}', 200, ['invoice_id' => 1]);
        // $this->assertSwaggerRequestResponse('PUT', '/api/user/invoices/{invoice_id}/payment', 200, ['invoice_id' => 1]);

        foreach ($this->response->getData()->invoices as $invoice) {
            $this->getJson('/api/user/invoices/' . $invoice->id);
            $this->assertNotEmpty($this->response->getData()->invoice->orders);

            if (! $this->response->getData()->invoice->paid_at) {
                $this->assertEmpty($this->response->getData()->invoice->paid_with);
                $this->putJson('/api/user/invoices/' . $invoice->id . '/payment');
                $this->getJson('/api/user/invoices/' . $invoice->id);
                $this->assertNotEmpty($this->response->getData()->invoice->paid_with);
                break;
            }
        }
    }
}
