<?php

namespace App\Models;

use App\Enums\InvoiceStatusType;
use App\Enums\OrderStatusType;
use App\Exceptions\OrderHasInvoiceException;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\Invoice
 * @property int $id
 * @property int $user_id
 * @property int $status
 * @property array|null $content
 * @property float|null $total_sum
 * @property array|null $payment_data
 * @property int|null $paid_with_card_id
 * @property array $payment_structure
 * @property int|null $user_balance_history_id
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $expire_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \App\Models\Card $paidWithCard
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @method static Builder|static open()
 * @mixin \Eloquent
 */
class Invoice extends Model implements AuditableInterface, NeedsAuditWhenCreated
{
    use AuditableTrait;
    use SoftDeletes;

    public const CURRENCY = 'USD';

    protected $dates = [
        'paid_at',
        'expire_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'status' => 'int',
        'content' => 'array',
        'payment_data' => 'array',
        'payment_structure' => 'array',
    ];

    protected $guarded = [];


    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            $model->expire_at = $model->asDate(today()->endOfMonth());
        });

        static::updated(function (self $invoice) {
            if ($invoice->isDirty('total_sum')) {
                $invoice->syncOriginalAttribute('total_sum');
                event('invoice.change_sum', $invoice);
            }

            if ($invoice->isDirty('status')) {
                $invoice->syncOriginalAttribute('status');
                event('invoice.status.' . strtolower(InvoiceStatusType::getKey($invoice->status)), $invoice);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function paidWithCard(): HasOne
    {
        return $this->hasOne(Card::class, 'id', 'paid_with_card_id');
    }

    public function addOrder(Order $order): void
    {
        if ($order->invoice_id) {
            throw new OrderHasInvoiceException("Order " . $order->id . " already has invoice " . $order->invoice_id);
        }
        // Invoice is like a home for orders
        $this->orders()->save($order);
        $this->calcTotalSum();
        $this->content = with($this->content, function (?array $content = null) use ($order) {
            $content[$order->id] = $order->price;

            return $content;
        });

        $this->save();
        event('invoice.order_added', $this);
    }

    public function calcTotalSum()
    {
        $this->total_sum = $this->orders()->whereNotIn('status', [
            OrderStatusType::CANCELLED,
            OrderStatusType::REFUNDED,
            OrderStatusType::FAILED,
        ])->sum('price');
    }

    public function scopeOpen(Builder $query): Builder
    {
        $query->where('status', '!=', InvoiceStatusType::PAID);

        return $query;
    }

    /**
     * Считаем сумму текущего ордера + всех остальных ордеров в статусах режектед или опен и сравниванем с кредитным лимитом юзера
     * @return bool
     */
    public function isLimitOverrun(): bool
    {
        $total_sum = $this->status === InvoiceStatusType::REJECTED || $this->status === InvoiceStatusType::OPEN ? $this->total_sum : 0;
        $total_sum += $this->whereIn('status', [InvoiceStatusType::REJECTED, InvoiceStatusType::OPEN])->where('id', '!=', $this->id)->sum('total_sum');

        return $this->user->credit_limit <= $total_sum;
    }

    public function changeStatus(int $newStatusCode, bool $instantSave = false)
    {
        $this->status = $newStatusCode;
        $instantSave ? $this->save() : true;
    }
}
