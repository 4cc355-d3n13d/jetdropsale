<?php declare(strict_types=1);

namespace App\Services;

use Http\Client\Common\HttpMethodsClient;
use Http\Client\Exception;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;

class CSCartClientService
{
    /** @throws Exception */
    public function get(string $url): string
    {
        $client = new HttpMethodsClient(HttpClientDiscovery::find(), MessageFactoryDiscovery::find());

        return (string) $client->get($url, $this->auth())->getBody();
    }

    public function auth(): array
    {
        return ['Authorization' => 'Bearer ' . env('CSCART_API_TOKEN', 'AUTH_TOKEN')];
    }
}
