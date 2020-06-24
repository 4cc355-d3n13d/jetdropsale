<?php

namespace App\Http\Transformers;

use App\Enums\InvoiceStatusType;
use App\Models\Invoice;
use League\Fractal;
use OpenApi\Annotations as OA;

class InvoiceTransformer extends Fractal\TransformerAbstract
{
    /**
     * @OA\Schema(
     *     schema="Invoice",
     *     @OA\Property(property="id", type="integer", example="1"),
     *     @OA\Property(property="sum", type="string", example="13.72"),
     *     @OA\Property(property="status", type="string", enum={"open", "to_pay", "paid", "rejected"}, example="paid"),
     *     @OA\Property(property="expire", type="string", example="2005-08-09T18:31:42+03:30"),
     *     @OA\Property(property="paid_at", type="string", x={"nullable": true}, example="2005-08-09T18:31:42+03:30"),
     *     @OA\Property(property="paid_with", type="object", x={"nullable": true}, allOf={@OA\Schema(ref="#/components/schemas/Card")})
     * )
     */

    /**
     * @OA\RequestBody(
     *     request="Invoice", required=true, description="User Invoice",
     *     @OA\JsonContent(ref="#/components/schemas/Invoice"),
     * )
     */

    protected $apiStatusMap = [
        InvoiceStatusType::OPEN => 'open',
        InvoiceStatusType::AWAITING_PAYMENT => 'processing',
        InvoiceStatusType::PROCESSING_PAYMENT => 'processing',
        InvoiceStatusType::PAID => 'paid',
        InvoiceStatusType::REJECTED => 'rejected',
        InvoiceStatusType::CANCELED => 'canceled',
        InvoiceStatusType::REFUNDED => 'refunded',
    ];

    public function getApiStatus(Invoice $invoice): string
    {
        return $this->apiStatusMap[$invoice->status];
    }

    public function transform(Invoice $invoice)
    {
        return [
            'id'         => (int) $invoice->id,
            'sum'        => (float) $invoice->total_sum,
            'status'     => $this->getApiStatus($invoice),
            'expire'     => $invoice->expire_at->format('c'),
            'paid_at'    => $invoice->paid_at ? $invoice->paid_at->format('c') : null,
            'paid_with'  => $invoice->paidWithCard ? [
                'brand'       => $invoice->paidWithCard->brand,
                'brand_image' => strtolower($invoice->paidWithCard->brand) . '.png',
                'last4'       => $invoice->paidWithCard->last4,
                'exp_month'   => $invoice->paidWithCard->exp_month,
                'exp_year'    => $invoice->paidWithCard->exp_year,
            ] : null
        ];
    }
}
