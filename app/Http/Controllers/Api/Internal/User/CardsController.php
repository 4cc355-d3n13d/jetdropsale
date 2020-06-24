<?php

namespace App\Http\Controllers\Api\Internal\User;

use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Transformers\CardTransformer;
use App\Models\Card;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use OpenApi\Annotations as OA;

/**
 * Class CardsController
 */
class CardsController extends ApiController
{
    /**
     * @OA\Get(path="/api/user/cards", operationId="getUserCards",
     *     tags={"User Cards"},
     *     summary="List user cards",
     *     description="Get list of cards that have been attached to the user account",
     *     @OA\Response(response="200", description="Gets user card list",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="cards", type="array", @OA\Items(
     *                  allOf={@OA\Schema(ref="#/components/schemas/Card")},
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="primary", type="boolean", example="true"),
     *             ))
     *         )
     *     )
     * )
     */
    public function getCards(): JsonResponse
    {
        $cards = Card::where('user_id', auth()->id())->get();

        return $this->success(new Collection($cards, new CardTransformer(), 'cards'));
    }

    /**
     * @OA\Put(path="/api/user/cards/{card_id}/primary", operationId="setPrimaryCard",
     *     tags={"User Cards"},
     *     summary="List user cards",
     *     description="Get list of cards that have been attached to the user account",
     *     @OA\Response(response="200", description="Gets user card list",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="card", type="array", @OA\Items(
     *                  allOf={@OA\Schema(ref="#/components/schemas/Card")},
     *                  @OA\Property(property="id", type="integer", example="1"),
     *                  @OA\Property(property="primary", type="boolean", example="true"),
     *             ))
     *         )
     *     )
     * )
     */
    public function setPrimary(Card $card): JsonResponse
    {
        $card->update(['primary' => true]);
        Card::where('id', '<>', $card->id)
            ->where('user_id', '=', auth()->id())
            ->update(['primary' => false])
        ;

        return $this->success(new Item($card->refresh(), new CardTransformer(), 'card'));
    }

    /**
     * @OA\Delete(path="/api/user/cards/{card_id}", operationId="removeUserCard",
     *     tags={"User Cards"},
     *     summary="Remove user card",
     *     description="Removes the user connected card",
     *     @OA\Parameter(@OA\Schema(type="integer"), name="card_id", in="path", required=true,
     *         description="Card identifier (reference) to remove"
     *     ),
     *     @OA\Response(response="200", description="The card was successfully removed from the users account",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *     ),
     *     @OA\Response(response="404", description="The user does not own the referenced card",
     *         @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     )
     * )
     */
    public function removeCard(int $cardId): JsonResponse
    {
        try {
            $cardToRemove = Card::where('user_id', Auth::user()->id)->where('id', $cardId)->firstOrFail();
            $cardToRemove->delete();

            // If remove primary card then make primary the last added one
            if ($cardToRemove->primary && $lastCard = Card
                ::where('user_id', Auth::user()->id)
                ->orderBy('created_at', 'desc')
                ->first()
            ) {
                $lastCard->update(['primary' => true]);
            }
        } catch (\Throwable $t) {
            abort(404, sprintf('Card with requested id (#%d) is not found', $cardId));
        }

        return $this->success();
    }

    /**
     * @OA\Post(path="/api/user/cards", operationId="addUserCard",
     *     tags={"User Cards"},
     *     summary="Add card to user",
     *     description="Add a card to the users account",
     *     @OA\RequestBody(ref="#/components/requestBodies/AddCard"),
     *     @OA\Response(response="200", description="Card was successfully added to the users account",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             allOf={@OA\Schema(ref="#/components/schemas/Card")},
     *             @OA\Property(property="id", type="integer", example="1"),
     *             @OA\Property(property="primary", type="boolean", example="true")
     *         )
     *     )
     * )
     */
    public function addCard(Request $request, PaymentService $paymentService): ?JsonResponse
    {
        if (! $token = $request->input('source')) {
            abort(Response::HTTP_BAD_REQUEST, 'Parameter "source" is missing or empty');
        }

        // Save card to db
        $card = new Card([
            'user_id' => auth()->id(),
            'billing_reference' => $token,
            'brand' => $request->input('brand'),
            'last4' => $request->input('last4'),
            'exp_month' => $request->input('exp_month'),
            'exp_year' => $request->input('exp_year'),
            'primary' => true,
        ]);

        try {
            $paymentService->attachCardToUser($card->user, $token);

            DB::beginTransaction();
            $card->save();
            $card
                ->where('id', '<>', $card->id)
                ->where('user_id', '=', $card->user->id)
                ->update(['primary' => false])
            ;
            DB::commit();

            return $this->success(new Item($card, new CardTransformer(), 'card'));
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::channel('billing')->error('Failed to attach the token, transaction rolled back');
            report($t);

            return abort(Response::HTTP_INTERNAL_SERVER_ERROR, $t->getMessage());
        }
    }
}
