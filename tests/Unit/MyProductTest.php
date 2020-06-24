<?php

namespace Tests\Unit;

use App\Models\Product\MyProduct;
use App\Models\Product\MyProductOption;
use App\Models\Product\MyProductTag;
use App\Models\Product\MyProductVariant;
use App\Services\MyProductService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class MyProductTest extends TestCase
{
    use DatabaseMigrations;
    // use UsesSqlite;

    public function testTagsWorkflow()
    {
        $emptyLine = '';
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);

        $tags = collect(explode(',', $emptyLine))->map(function (string $tag) use ($myProduct) {
            return $tag = trim($tag) ? MyProductTag::firstOrCreate([
                'title' => $tag,
                'user_id' => auth()->id(),
                'my_product_id' => $myProduct->id,
            ]) : null;
        })->filter(function (?MyProductTag $tag) {
            return (bool) $tag;
        });

        self::assertEmpty($tags);
    }

    /**
     * @test
     */
    public function splitByOption()
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);
        // Creating options
        $colors = ['red', 'blue', 'yellow'];
        $size = ['L','S','M', 'XL', 'XXL'];
        $colorOptions = make(MyProductOption::class, ['name' => 'Color', 'ali_option_id' => '1'], count($colors))
            ->map(function (MyProductOption $myProduct, $key) use ($colors) {
                $myProduct->value = $colors[$key];
                $myProduct->ali_sku = $key+1;
                return $myProduct;
            });
        $sizeOptions = make(MyProductOption::class, ['name' => 'Size', 'ali_option_id' => '2'], count($size))
            ->map(function (MyProductOption $myProduct, $key) use ($size) {
                $myProduct->value = $size[$key];
                $myProduct->ali_sku = $key+1+10;
                return $myProduct;
            });
        $myProduct->options()->saveMany($colorOptions);
        $myProduct->options()->saveMany($sizeOptions);

        /** @var Collection $colorOptions */
        // Creating product variants from options
        $variants = $colorOptions->crossJoin($sizeOptions)->map(function ($options) use ($myProduct) {
            return make(MyProductVariant::class, ['my_product_id' => $myProduct->id, 'combination' => collect($options)->pluck('ali_sku', 'ali_option_id')->toJson()]);
        });

        $myProduct->combinations()->saveMany($variants);

        $this->assertCount(count($colors) * count($size), $myProduct->combinations);
        $this->assertCount(count($colors) + count($size), MyProductOption::all());
        $this->assertCount(1, MyProduct::all());
        //dd(MyProductVariant::all()->toArray());
        $myProduct->options->each(function (MyProductOption $myOption) {
            $this->assertNotEmpty(MyProductVariant::whereJsonContains('combination', [$myOption->ali_option_id => $myOption->ali_sku ])->count());
        });
        // Splitting by the color


        $ms = new MyProductService();
        $ms->split($myProduct, auth()->user(), $colorOptions->first()->id);
        // Wanna see 3 more products
        $this->assertCount(4, MyProduct::all());
        // Check every new splitted product
        MyProduct::where('id', '!=', $myProduct->id)->get()->each(function (MyProduct $splitProduct, $key) use ($myProduct, &$colors, $size) {
            // Check the title is correct...
            $this->assertEquals("[Color:" . array_shift($colors) ."] " . $myProduct->title, $splitProduct->title);
            // Only 1 size option left (and 5 total)...
            $this->assertCount(count($size), $splitProduct->options);
            $this->assertCount(count($size), $splitProduct->options()->where('name', 'Size')->whereIn('value', $size)->get());

            // And every product has 5 combinations...
            $this->assertCount(5, $splitProduct->combinations);

            // And check the every combination...
            $splitProduct->options->each(function (MyProductOption $myOption) use ($splitProduct) {
                $this->assertCount(1, MyProductVariant::where(['my_product_id'=>$splitProduct->id])->whereJsonContains('combination', [$myOption->ali_option_id => $myOption->ali_sku])->get());
            });
        });
    }
}
