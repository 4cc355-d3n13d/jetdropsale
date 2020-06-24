<?php

namespace App\Http\Transformers;

use App\Models\Card;
use League\Fractal;
use OpenApi\Annotations as OA;

class CardTransformer extends Fractal\TransformerAbstract
{
    /**
     * @OA\Schema(
     *     schema="Card",
     *     @OA\Property(property="brand", type="string", example="Visa"),
     *     @OA\Property(property="brand_image", type="string", example="visa.png"),
     *     @OA\Property(property="last4", type="integer", example="4242"),
     *     @OA\Property(property="exp_month", type="integer", example="11"),
     *     @OA\Property(property="exp_year", type="integer", example="2022")
     * )
     */

    /**
     * @OA\RequestBody(request="AddCard", required=true, description="User bank card",
     *     @OA\JsonContent(
     *         allOf={@OA\Schema(ref="#/components/schemas/Card")},
     *         @OA\Property(property="source", type="string", example="src_1D9EaTIJb8S3vLIy3zMX0I4y")
     *     )
     * )
     */

    public function transform(Card $card)
    {
        return [
            'id'          => (int) $card->id,
            'brand'       => $card->brand,
            'brand_image' => env('CARD_IMAGE_PATH', '/images/cardbrand/') . strtolower($card->brand) . '.png',
            'last4'       => (int) $card->last4,
            'exp_month'   => (int) $card->exp_month,
            'exp_year'    => (int) $card->exp_year,
            'primary'     => (bool) $card->primary,
        ];
    }
}
