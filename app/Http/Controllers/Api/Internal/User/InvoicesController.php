<?php /** @noinspection PhpInconsistentReturnPointsInspection */

namespace App\Http\Controllers\Api\Internal\User;

use App\Http\Controllers\Api\Internal\ApiController;
use App\Http\Transformers\InvoiceDetailedTransformer;
use App\Http\Transformers\InvoiceTransformer;
use App\Jobs\ProcessInvoicePayment;
use App\Models\Invoice;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use OpenApi\Annotations as OA;

/**
 * Class InvoicesController
 */
class InvoicesController extends ApiController
{
    /** @var PaymentService */
    private $paymentService;


    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * @OA\Get(path="/api/user/invoices", operationId="getInvoiceList",
     *     tags={"User Invoices"},
     *     summary="List user invoices",
     *     description="Get the lists of all user invoices",
     *     @OA\Response(response="200", description="User invoices (list)",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="invoices", type="array", @OA\Items(ref="#/components/schemas/Invoice")),
     *             @OA\Property(property="limits", type="array", @OA\Items(type="string"))
     *         )
     *     ),
     * )
     */
    public function getInvoiceList()
    {
        /** @var User $user */
        $user = auth()->user();
        $resource = new Collection($user->invoices, new InvoiceTransformer(), 'invoices');
        /** @noinspection PhpUndefinedMethodInspection */
        $resource->setMetaValue('limits', [
            'credit' => $user->credit_limit - $user->invoices()->open()->sum('total_sum'),
        ]);

        return $this->success($resource);
    }

    /**
     * @OA\Get(path="/api/user/invoices/{invoice_id}", operationId="getInvoiceData",
     *     tags={"User Invoices"},
     *     summary="Detailed invoice data",
     *     description="Gets detailed info about the invoice",
     *     @OA\Parameter(@OA\Schema(type="integer"), name="invoice_id", in="path", required=true,
     *         description="Invoice identificator (reference) to process. Response fields `paid_at` & `paid_with` are nullable."
     *     ),
     *     @OA\Response(response="200", description="Detailed invoice info",
     *         @OA\JsonContent(
     *             allOf={@OA\Schema(ref="#/components/schemas/SuccessfulResponse")},
     *             @OA\Property(property="invoice", type="object", allOf={@OA\Schema(ref="#/components/schemas/Invoice")})
     *         )
     *     )
     * )
     */
    public function getDetailedInvoiceData(int $invoiceId)
    {
        try {
            $invoice = Invoice::where('user_id', Auth::user()->id)->where('id', $invoiceId)->firstOrFail();

            return $this->success(new Item($invoice, new InvoiceDetailedTransformer(), 'invoice'));
        } catch (\Throwable $t) {
            abort(Response::HTTP_BAD_REQUEST, sprintf('Invoice with requested id (#%d) is not found', $invoiceId));
        }
    }

    /**
     * @OA\Put(path="/api/user/invoices/{invoice_id}/payment", operationId="requestInvoicePayment",
     *     tags={"User Invoices"},
     *     summary="Request invoice payment",
     *     description="Send request to process the invoice payment",
     *     @OA\Parameter(@OA\Schema(type="integer"), name="invoice_id", in="path", required=true,
     *         description="Invoice identificator (reference) to process"
     *     ),
     *     @OA\Response(response="200", description="Invoice payment successfully requested",
     *         @OA\JsonContent(ref="#/components/schemas/SuccessfulResponse")
     *     ),
     *     @OA\Response(response="404", description="The invoice was not found in the current user account (wrong invoice_id)",
     *         @OA\JsonContent(ref="#/components/schemas/FailedResponse")
     *     )
     * )
     */
    public function requestInvoicePayment(int $id)
    {
        try {
            ProcessInvoicePayment::dispatch(Invoice::findOrFail($id));

            return $this->success();
        } catch (ModelNotFoundException $e) {
            abort(Response::HTTP_BAD_REQUEST, 'The invoice was not found in the current user account (wrong invoice_id)');
        } catch (\Throwable $t) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Service is temporary unavailable. Please try again later.');
        }
    }
}
