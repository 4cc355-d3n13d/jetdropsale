<?php

namespace App\Console\Commands;

use App\Enums\ProductStatusType;
use App\Models\Product\Product;
use Illuminate\Console\Command;

class ProductsElastic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:elastic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send products to elastic index queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = 0;
        $total = 0;
        do {
            $products = Product::where('id', '>', $id)
                ->where('is_available', ProductStatusType::AVAILABLE)
                ->orderBy('id')->limit(config('scout.chunk.searchable'))->get();

            if ($products->isNotEmpty()) {
                $id = $products->pluck('id')->max();
                $total += $products->count();
                $this->info('Get ' . $products->count() . ' products, max id = ' . $id);
                $products->first()->queueMakeSearchable($products);
            }
        } while ($products->isNotEmpty());
        $this->info("Total products send to queue: "  . $total);
    }
}
