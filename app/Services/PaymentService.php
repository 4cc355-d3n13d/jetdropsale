<?php

namespace App\Services;

use App\Enums\InvoiceStatusType;
use App\Models\Card;
use App\Models\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\GatewayInterface;
use Omnipay\Stripe\Gateway;

class PaymentService
{
    /** @var Gateway */
    protected $gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->gateway = $gateway;
    }

    /** @throws \RuntimeException */
    public function attachCardToUser(User $user, string $token): void
    {
        // Add card to the payment gateway
        $user->billing_reference
            ? $this->attachTokenToCustomer($user, $token)
            : $this->createCustomerWithToken($user, $token)
        ;
    }

    private function attachTokenToCustomer(User $user, string $token): void
    {
        $response = $this->gateway->attachSource([
            'customerReference' => $user->billing_reference,
            'source' => $token,
        ])->send();

        if (! $response->isSuccessful()) {
            Log::channel('billing')->error(
                sprintf('Failed to add source for user %s', $user->email),
                $response->getData()
            );

            throw new \RuntimeException($response->getMessage());
        }
    }

    private function createCustomerWithToken(User $user, string $token): void
    {
        $response = $this->gateway->createCustomer([
            'email' => $user->email,
            'source' => $token,
        ])->send();

        if (! $response->isSuccessful()) {
            Log::channel('billing')->error(
                sprintf('Failed to create customer for user %s', $user->email),
                $response->getData()
            );

            throw new \RuntimeException($response->getMessage());
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $customerId = $response->getCustomerReference();
        $user->billing_reference = $customerId;
        $user->save();
    }

    public function payInvoice(Invoice $invoice): bool
    {
        $this->log(['Started invoice #%s payment preparation', $invoice->id]);
        DB::beginTransaction();

        try {
            $selectQuery = Invoice
                ::where(['id' => $invoice->id])
                ->where('status', '!=', InvoiceStatusType::PROCESSING_PAYMENT)
            ;

            if (! $selectQuery->exists()) {
                throw new \InvalidArgumentException(
                    sprintf('Invoice #%s for user %s is not ready to be charged', $invoice->id, $invoice->user->email)
                );
            }

            /** @var Invoice $freshInvoice */
            $freshInvoice = $selectQuery->lockForUpdate()->first();

            if (InvoiceStatusType::PAID === $freshInvoice->status) {
                throw new \InvalidArgumentException(
                    sprintf('Invoice #%s for user %s has already been paid', $invoice->id, $invoice->user->email)
                );
            }

            $freshInvoice->status = InvoiceStatusType::PROCESSING_PAYMENT;
            $freshInvoice->save();
            DB::commit();
        } catch (\Throwable $t) {
            DB::rollBack();
            $this->log('Failed to lock the invoice, transaction rolled back', (array) $t->getMessage(), 'error');
            report($t);

            return false;
        }

        return $this->processInvoicePayment($invoice->refresh());
    }

    private function processInvoicePayment(Invoice $invoice): bool
    {
        try {
            /** @var Card $card */
            $card = $invoice->user->getPrimaryCard();
        } catch (ModelNotFoundException $e) {
            $errorText = sprintf('User %s has no primary card, cancel payment', $invoice->user->email);
            $this->log($errorText, (array) $e->getMessage());
            $invoice->refresh();
            $invoice->payment_data = ['error' => $errorText];
            $invoice->changeStatus(InvoiceStatusType::REJECTED, true);

            return false;
        }

        $this->log(['Started invoice #%s payment process', $invoice->id]);

        $sumToWithdrawFromCard = $invoice->total_sum;
        if ($invoice->user->balance) {
            $restOfBalance = $invoice->user->balance - $invoice->total_sum;
            $sumToWithdrawFromCard = $restOfBalance < 0 ? abs($restOfBalance) : 0;
        }

        if (! $sumToWithdrawFromCard) {
            // We have nothing to withdraw
            DB::beginTransaction();
            try {
                $invoice->user->changeBalance($invoice->total_sum * -1, $invoice);
                $invoice->status = InvoiceStatusType::PAID;
                $invoice->paid_at = now();

                $paymentStructure = $invoice->payment_structure;
                $paymentStructure['user_balance'] = $invoice->total_sum;
                $invoice->payment_structure = $paymentStructure;

                $invoice->save();

                DB::commit();
            } catch (\Throwable $t) {
                DB::rollBack();
                $this->log('Error performing invoice payment from user balance', (array) $t->getMessage(), 'error');

                return false;
            }

            return true;
        }


        // There are something on a balance (about 0)...

        $paymentStructure = $invoice->payment_structure;
        if ($invoice->user->balance) {
            $paymentStructure['user_balance'] = $invoice->user->balance;
        }
        $paymentStructure['card'] = $sumToWithdrawFromCard;
        $invoice->payment_structure = $paymentStructure;

        DB::beginTransaction();
        try {
            $successfulPayment = false;
            $response = $this->gateway->purchase([
                'amount' => $sumToWithdrawFromCard,
                'currency' => Invoice::CURRENCY,
                'cardReference' => $card->billing_reference,
                'customerReference' => $invoice->user->billing_reference,
                'description' => 'Dropwow Invoice #' . $invoice->getKey(),
            ])->send();

            if ($response->isSuccessful()) {
                $this->log([
                    'Invoice charge transaction was successful (ref: %s; original message: %s)',
                    $response->getTransactionReference(),
                    $response->getMessage()
                ]);
                $invoice->payment_data = $response->getData();
                $invoice->paid_at = new Carbon();
                $invoice->paid_with_card_id = $card->id;
                $invoice->changeStatus(InvoiceStatusType::PAID, true);
                $successfulPayment = true;
            } elseif ($response->isRedirect()) {
                $this->log([
                    'Invoice charge transaction requested redirect (ref %s; original message: %s)',
                    $response->getTransactionReference(),
                    $response->getMessage()
                ]);
                $invoice->payment_data = $response->getData();
                $invoice->changeStatus(InvoiceStatusType::REJECTED, true);
            } else {
                $this->log([
                    'Invoice charge transaction was NOT successful (ref: %s; original message: %s)',
                    $response->getTransactionReference(),
                    $response->getMessage()
                ], $response->getData(), 'error');
                $invoice->payment_data = $response->getData();
                $invoice->changeStatus(InvoiceStatusType::REJECTED, true);
            }
            $invoice->save();
            if ($successfulPayment) {
                $invoice->user->changeBalance($invoice->user->balance * -1, $invoice);
            }
            DB::commit();
        } catch (\Throwable $t) {
            DB::rollBack();
            $this->log('Error performing invoice payment from card', (array) $t->getMessage(), 'error');
        }

        return $successfulPayment;
    }

    /**
     * @param string|array $message
     */
    protected function log($message, array $context = [], string $level = 'info', string $channel = 'billing'): void
    {
        Log::channel($channel)->$level(
            is_array($message)
                ? call_user_func_array('sprintf', $message)
                : (string) $message,
            $context
        );
    }
}
