<?php

namespace App\Http\Controllers\Api\Internal\User;

use App\Enums\OrderStatusType;
use App\Exceptions\OrderStatusException;
use App\Filters\OrderFilters;
use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Resources\OrdersCollection;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class OrdersController extends ApiController
{
    /**
     * @OA\Schema(
     *     schema="Order",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="sum", type="number", example="13.72"),
     *     @OA\Property(property="status", type="string", enum={"open", "to_pay", "paid", "rejected"}, example="paid"),
     *     @OA\Property(property="expire", type="string", example="2005-08-09T18:31:42+03:30"),
     *     @OA\Property(property="paid_at", type="string", x={"nullable": true}, example="2005-08-09T18:31:42+03:30"),
     *     @OA\Property(property="paid_with", type="object", x={"nullable": true}, allOf={@OA\Schema(ref="#/components/schemas/Card")})
     * )
     */

    /**
     * @OA\RequestBody(
     *     request="Order", required=true, description="User Order",
     *     @OA\JsonContent(ref="#/components/schemas/Order"),
     * )
     */

    /**
     * @OA\Get(path="/api/user/orders",
     *     tags={"User orders"},
     *     summary="Get user's orders",
     *     description="Get user's orders",
     *     @OA\Response(response="200", description="User's settings list successfully extracted",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="orders", type="array", @OA\Items(ref="#/components/schemas/Order"))
     *         )
     *     )
     * )
     */
    public function list(OrderFilters $filters)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return new OrdersCollection(auth()->user()->orders()->filter($filters)->latest()->get(), 'orders');
    }

    /**
     * @OA\Put(path="/api/user/orders/{order_id}/status/{new_status}",
     *     tags={"User orders"},
     *     summary="Change order status",
     *     description="Change order status",
     *     @OA\Parameter(@OA\Schema(type="integer"), name="order_id", in="path", required=true,
     *         description="Order identificator (number) to process"
     *     ),
     *     @OA\Parameter(@OA\Schema(type="string"), name="new_status", in="path", required=true,
     *         description="New status (string) to set up"
     *     ),
     *     @OA\Response(response="200", description="Order status successfully changed",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *     ),
     *     @OA\Response(response="400", description="Unacceptable new status",
     *         @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     ),
     *     @OA\Response(response="403", description="Foreign order",
     *         @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     )
     * )
     */
    public function changeStatus(Order $order, string $newStatus): JsonResponse
    {
        if (! $order->changeStatus(OrderStatusType::getValue($newStatus))) {
            abort(Response::HTTP_BAD_REQUEST, 'Unacceptable new status');
        }

        return $this->success();
    }

    /**
     * @OA\Put(path="/api/user/orders/mass-status",
     *     tags={"User orders"},
     *     summary="Mass change orders statuses",
     *     description="Mass change orders statuses",
     *     @OA\Response(response="200", description="User's settings list successfully extracted",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *     ),
     * )
     */
    public function changeStatuses(Request $request): JsonResponse
    {
        $results = [];
        collect($request)->each(function ($statusString, $orderId) use (&$results) {
            $results[$orderId] = transform(Order::find($orderId), function (Order $order) use ($statusString) {
                try {
                    $statusCode = OrderStatusType::getValue($statusString);
                    $order->changeStatus($statusCode);
                    $order->refresh();
                } catch (OrderStatusException $e) {
                } catch (\Exception $e) {
                }

                return OrderStatusType::getKey($order->status);
            });
        });

        return $this->success(['statuses' => $results]);
    }

    /**
     * @OA\Get(path="/api/user/orders/statuses",
     *     tags={"User orders"},
     *     summary="Orders statuses",
     *     description="Available order statuses to use with html select element",
     *     @OA\Response(response="200", description="Order status list",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *     ),
     * )
     */
    public function listStatuses()
    {
        return $this->success([
            'statuses' => OrderStatusType::getUserStatusList()
        ]);
    }
}
