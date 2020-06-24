<?php

namespace Tests\Feature\Sync;

use App\Services\CSCartClientService;
use Tests\TestCase;

class MarketApiTest extends TestCase
{
    private const SHOP = 'dev-dropwow.myshopify.com';

    /** @throws \Http\Client\Exception */
    public function testNotAuthorized(): void
    {
        $client = $this->createMock(CSCartClientService::class);
        $client->method('auth')->willReturn([]);
        app()->instance(CSCartClientService::class, $client);
        /** @var CSCartClientService $client */
        $client = app()->make(CSCartClientService::class);
        $result = $client->get(env('CSCART_API_MIGRATION_ENDPOINT') . '?shop=' . self::SHOP);

        self::assertEmpty($result);
    }

    /** @throws \Http\Client\Exception */
    public function testAuthorized(): void
    {
        /** @var CSCartClientService $client */
        $client = app()->make(CSCartClientService::class);
        $result = json_decode($client->get(env('CSCART_API_MIGRATION_ENDPOINT') . '?shop=' . self::SHOP));

        self::assertNotEmpty($result->data->orders);
    }
}
