<?php

namespace App\Console\Commands;

use App\Services\AliProductService;
use Http\Discovery\HttpClientDiscovery;
use Http\Discovery\MessageFactoryDiscovery;
use Illuminate\Console\Command;

class FeedBot extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'feed:ali-bot';

    /**
     * The console command description.
     */
    protected $description = 'Feed the ali-parser bot with ali product ids. Scheduled';

    /**
     * Execute the console command.
     * @throws \Http\Client\Exception
     */
    public function handle()
    {
        $httpClient = HttpClientDiscovery::find();
        $requestFactory = MessageFactoryDiscovery::find();

        $url = sprintf('%s?datetime_start=%s&datetime_end=%s&all_webmasters=%s&limit=%s',
            config('settings.admitad.ali.parse.api'),
            now()->subMinutes(config('settings.admitad.ali.parse.data_interval'))->format('Y-m-d\TH:i:s'),
            now()->format('Y-m-d\TH:i:s'),
            config('settings.admitad.ali.parse.all_webmasters'),
            config('settings.admitad.ali.parse.limit')
        );

        $request = $requestFactory->createRequest('GET', $url, ['Authorization' => 'Token: ' . config('settings.admitad.ali.parse.token')]);
        $response = (string) $httpClient->sendRequest($request)->getBody();
        $results = json_decode($response)->results;

        $aliImportService = new AliProductService();
        foreach ($results as $result) {
            $aliImportService->fetchById($result->product_id);
        }
    }
}
