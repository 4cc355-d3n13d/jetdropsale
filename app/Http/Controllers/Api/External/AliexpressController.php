<?php /** @noinspection PhpDynamicAsStaticMethodCallInspection */

namespace App\Http\Controllers\Api\External;

use App\Enums\ProductStatusType;
use App\Jobs\ImportAliProduct;
use App\Models\Product\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * Class AliProductController
 */
class AliexpressController extends ApiController
{
    public function save(): JsonResponse
    {
        $productJson = request()->getContent();
        $productData = json_decode($productJson, true);

        if (! $productData) {
            Log::error('Не удалось прочитать данные для загрузки продукта от али: ' . $productJson);

            abort(Response::HTTP_NOT_ACCEPTABLE, 'Failed accepting product');
        }

        ImportAliProduct::dispatch($productData);

        return $this->success('Product accepted');
    }

    public function list(): array
    {
        $products = Product::where('is_available', ProductStatusType::AVAILABLE)
            ->oldest('updated_at')->select(['id','ali_id'])
            ->take(1000)->get()->pluck('ali_id', 'id');
        $now = now();
        Product::whereIn('id', $products->keys())->update(['updated_at' => $now]);
        return [
            'products' =>$products->values()
        ];
    }

    public function delete()
    {
        $productData = json_decode(request()->getContent(), true);
        $product = Product::where('ali_id', $productData['product_code'])->first();
        if (!$product) {
            return $this->success('product not found');
        }

        $product->is_available = ProductStatusType::UNAVAILABLE;
        $product->save();
        $product->unsearchable();
        return $this->success('product deleted');
    }
}
