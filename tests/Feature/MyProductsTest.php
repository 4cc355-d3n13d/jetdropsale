<?php

namespace Tests\Feature;

use App\Enums\MyProductStatusType;
use App\Models\Product\MyProduct;
use App\Models\Product\MyProductCollection;
use App\Models\Product\MyProductTag;
use App\Models\Shopify\Shop;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\UsesSqlite;

class MyProductsTest extends TestCase
{
    use DatabaseMigrations;
    use UsesSqlite;

    /** @test */
    public function userSeeMyProductsList(): void
    {
        $this->signIn();
        create(Shop::class, ['user_id'=>auth()->id()]);
        $myProduct1 = create(MyProduct::class, ['user_id' => auth()->id()]);
        $myProduct2 = create(MyProduct::class, ['user_id' => auth()->id()]);
        $myProductNonConnected = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::NON_CONNECTED]);
        $notMyProduct = create(MyProduct::class);

        $this
            ->getJson('api/my-products')
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
                'id' => $myProduct1->id
            ])
            ->seeJson([
                'result' => 'ok',
                'id' => $myProduct2->id
            ])
            ->seeJson([
                'result' => 'ok',
                'id' => $myProductNonConnected->id
            ])
            ->dontSeeJson([
                'id' => $notMyProduct->id
            ])
            ->seeJsonStructure([
                'my_products' => [
                    '*' => [
                        'id',
                        'title',
                        'status',
                        'is_shopify_send_pending',
                        'price',
                        'amount',
                        'description',
                        'image',
                        'images',
                        'ali_id',
                        'product_id',
                        'shopify_product_id',
                        'product_categories_id',
                        'user_id',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'stats',
                'pagination' => [
                    'total',
                    'count',
                    'per_page',
                    'current_page',
                    'total_pages',
                    'links',
                ],
            ])
            ->seeJsonSubset([
                'stats' => [
                    'total' => 3,
                    'connected' => 2,
                    'non_connected' => 1
                ]
            ]);
        ;
    }
    
    public function testAuthUserSeeMyProductsFilterConnected(): void
    {
        $this->signIn();
        create(Shop::class, ['user_id'=>auth()->id()]);
        $myProductConnected1 = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::CONNECTED]);
        $myProductConnected2 = create(MyProduct::class, ['user_id' =>  auth()->id(), 'status' => MyProductStatusType::CONNECTED]);
        $myProductNonConnected = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::NON_CONNECTED]);
        $notMyProduct = create(MyProduct::class);

        $response = $this->json('GET', 'api/my-products', ['product_status' => 'connected']);

        $response
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
                'id' => $myProductConnected1->id
            ])
            ->seeJson([
                'result' => 'ok',
                'id' => $myProductConnected2->id
            ])
            ->dontSeeJson(['id' => $myProductNonConnected->id])
            ->dontSeeJson(['id' => $notMyProduct->id])
            ->seeJsonSubset([
                'stats' => [
                    'total' => 3,
                    'connected' => 2,
                    'non_connected' => 1
                ]
            ])
        ;
    }

    public function testAuthUserSeeMyProductsFilterNonConnected(): void
    {
        $this->signIn();
        create(Shop::class, ['user_id'=>auth()->id()]);
        $myProductConnected1 = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::CONNECTED]);
        $myProductConnected2 = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::CONNECTED]);
        $myProductNonConnected = create(MyProduct::class, ['user_id' => auth()->id(), 'status' => MyProductStatusType::NON_CONNECTED]);
        $notMyProduct = create(MyProduct::class);

        $response = $this->json('GET', 'api/my-products', ['product_status' => 'non_connected']);

        $response
            ->assertResponseOk()
            ->seeJson([
                'result' => 'ok',
                'id' => $myProductNonConnected->id
            ])
            ->dontSeeJson(['id' => $notMyProduct->id])
            ->dontSeeJson(['id' => $myProductConnected1->id])
            ->dontSeeJson(['id' => $myProductConnected2->id])
            ->seeJsonSubset([
                'stats' => [
                    'total' => 3,
                    'connected' => 2,
                    'non_connected' => 1
                ]
            ])
        ;
    }

    /** @test */
    public function userCanSeeHisProductById(): void
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);

        $this->getJson($myProduct->path())
            ->assertResponseOk()
            ->seeJson(['result' => 'ok', 'id' => $myProduct->id]);
        ;
    }

    /** @test */
    public function userCantSeeNotHisProductById(): void
    {
        $this->signIn();
        $myProduct = create(MyProduct::class);

        $this
            ->getJson($myProduct->path())
            ->assertResponseStatus(403)
            ->dontSeeJson(['result' => 'ok', 'id' => $myProduct->id])
        ;
    }

    /** @test */
    public function userCantAddNonExistentProduct(): void
    {
        $this->signIn();

        $this
            ->putJson('/api/my-products/1', ['title' => 'new product title'])
            ->assertResponseStatus(404)
            ->seeJsonContains(['result' => 'error'])
        ;
    }

    /** @test */
    public function userCanEditHisProductWithTags(): void
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);

        $this
            ->putJson($myProduct->path(), [
                'type' => 'new product type',
                'title' => 'new product title',
                'vendor' => 'new product vendor',
                'tags' => 'new, product, vendor',
            ])
        ;

        $this
            ->assertResponseStatus(200)
            ->seeJsonContains([
                'type' => 'new product type',
                'title' => 'new product title',
                'vendor' => 'new product vendor'
            ])
            ->seeInDatabase('my_product_tags', ['title' => 'new'])
            ->seeInDatabase('my_product_tags', ['title' => 'vendor'])
            ->seeInDatabase('my_product_tags', ['title' => 'product'])
        ;
    }

    /** @test */
    public function userCanCreateAndSeeAndAttachCollection()
    {
        $this->withExceptionHandling();
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);
        $this->post(route('my-product.collection.create'), ['title' => 'title1'])->seeJson(['result' => 'ok']);
        $this->post(route('my-product.collection.create'), ['title' => 'title2'])->seeJson(['result' => 'ok']);
        $this->seeInDatabase('my_collections', ['title' => 'title1', 'user_id' => auth()->id()]);
        $this->seeInDatabase('my_collections', ['title' => 'title2', 'user_id' => auth()->id()]);
        $this->assertCount(2, MyProductCollection::all());
        $this
            ->get(route('my-product.collection.find'))
        ;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->assertEquals(['collections' => [
            ['id' => '1', 'title' => 'title1'],
            ['id' => '2', 'title' => 'title2'],
        ], 'result' => 'ok'], $this->response->getData(true));

        $this
            ->putJson($myProduct->path(), [
                'type' => 'new product type',
                'title' => 'new product title',
                'vendor' => 'new product vendor',
                'collections' => [1, 2],
            ])
        ;

        $this->seeInDatabase('my_product_has_collections', ['my_product_id' => $myProduct->id, 'collection_id' => '1']);
        $this->seeInDatabase('my_product_has_collections', ['my_product_id' => $myProduct->id, 'collection_id' => '2']);
    }


    /** @test */
    public function userCanEditHisProductWithCollections(): void
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id' => auth()->id()]);
        /** @var MyProductCollection $myCollection */
        $myCollection = create(MyProductCollection::class, ['title' => 'title1', 'user_id' => auth()->id()]);

        $this
            ->putJson($myProduct->path(), [
                'type' => 'new product type',
                'title' => 'new product title',
                'vendor' => 'new product vendor',
                'collections' => [[$myCollection->id => $myCollection->title], ['title2']],
            ])
        ;

        $this->seeInDatabase('my_collections', ['user_id' => auth()->id(), 'title' => 'title1']);
        $this->seeInDatabase('my_collections', ['user_id' => auth()->id(), 'title' => 'title2']);
        $this->seeInDatabase('my_product_has_collections', ['my_product_id' => $myProduct->id, 'my_collection_id' => $myCollection->id]);
    }

    /** @test */
    public function userCanDeleteCollection()
    {
        $this->signIn();
        $collection = create(MyProductCollection::class, ['user_id' => auth()->id()]);
        $this
            ->delete(route('my-product.collection.delete', ['id' => $collection->id]))
            ->seeJson(['message' => 'The collection was successfully deleted'])
        ;

        $this->dontSeeInDatabase('my_product_has_collections', ['id' => $collection->id]);
    }

    /** @test */
    public function userCanDeleteTag()
    {
        $this->signIn();
        $tag = create(MyProductTag::class, ['user_id' => auth()->id()]);
        $this
            ->delete(route('my-product.tag.delete', ['id' => $tag->id]))
            ->seeJson(['message' => 'The tag was successfully deleted'])
        ;

        $this->dontSeeInDatabase('my_product_tags', ['id' => $tag->id]);
    }

    /** @test */
    public function userCantEditNotHisProduct(): void
    {
        $this->signIn();
        $notMyProduct = create(MyProduct::class);

        $this
            ->putJson($notMyProduct->path(), ['title' => 'new product title'])
            ->assertResponseStatus(403)
        ;
    }

    /** @test */
    public function aGuestDontSeeMyProducts(): void
    {
        create(MyProduct::class);

        $this
            ->getJson('api/my-products')
            ->assertResponseStatus(401)
        ;
    }

    /** @test */
    public function aGuestDontSeeMyProductById(): void
    {
        $product = create(MyProduct::class);
        $this
            ->getJson($product->path())
            ->assertResponseStatus(401)
        ;
    }

    /** @test */
    public function aGuestCantEditMyProduct(): void
    {
        $notMyProduct = create(MyProduct::class);

        $this
            ->putJson($notMyProduct->path(), ['title' => 'new product title'])
            ->assertResponseStatus(401)
        ;
    }

    /** @test */
    public function aUserCanDeleteImagesFromMyProduct()
    {
        $this->withoutExceptionHandling();
        $this->signIn();
        $image = '/asdf.jpg';
        $images = ['1.jpg', '2.jpg', '3.jpg'];
        $myProduct = create(MyProduct::class, ['user_id'=>auth()->id(), 'image'=>$image, 'images' => json_encode($images)]);

        $this->assertSame($image, $myProduct->image);

        $this->delete('api/my-products/' . $myProduct->id . '/images', [$image, '2.jpg'])
            ->assertResponseStatus(200)
            ->seeJson(['message' => 'The images were successfully deleted']);

        $myProduct->refresh();
        $this->assertEmpty($myProduct->image);
        $this->assertEquals(json_encode(Arr::sortRecursive(["1.jpg","3.jpg"])), $myProduct->images);
    }

    /** @test */
    public function userCanUploadAndDeleteImageForMyProduct()
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id'=>auth()->id(), 'images'=>""]);

        Storage::fake('public');
        $this->json('POST', 'api/my-products/' . $myProduct->id . '/image', [
            'image' => $file = UploadedFile::fake()->image('product2.jpg')
        ])->assertResponseStatus(200)->seeJson(['result' => 'ok']);


        $newFileName = 'images/products/' . $myProduct->id . '/' . $file->hashName();
        $this->assertEquals(json_encode([$newFileName]), $myProduct->fresh()->images);
        Storage::disk('public')->assertExists($newFileName);

        $this->delete('api/my-products/' . $myProduct->id . '/images', [$newFileName])
            ->assertResponseStatus(200)
            ->seeJson(['message' => 'The images were successfully deleted'])
        ;

        // Storage::disk('public')->assertMissing($newFileName); // Should not delete
    }

    /** @test */
    public function userCanUploadImagesForMyProduct()
    {
        $this->signIn();
        $myProduct = create(MyProduct::class, ['user_id'=>auth()->id(), 'images'=>""]);

        Storage::fake('public');
        $this->json('POST', 'api/my-products/' . $myProduct->id . '/images', [
            'image[0]' => $file0 = UploadedFile::fake()->image('product1.jpg'),
            'image[1]' => $file1 = UploadedFile::fake()->image('product2.jpg'),
            'image[2]' => $file2 = UploadedFile::fake()->image('product3.jpg')
        ])
            ->assertResponseStatus(200)
            ->seeJson(['result' => 'ok']);

        $images = collect(json_decode($myProduct->fresh()->images));
        $this->assertCount(3, $images);

        $images->each(function ($imagePath, $i) use ($myProduct, $file0, $file1, $file2) {
            $newFileName = 'images/products/' . $myProduct->id . '/' . ${"file".$i}->hashName();
            $this->assertEquals($newFileName, $imagePath);
            Storage::disk('public')->assertExists($newFileName);
        });
    }
}
