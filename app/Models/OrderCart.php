<?php

namespace App\Models;

use App\Models\Product\ProductGoodsTrait;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\OrderCart
 *
 * @property int $id
 * @property string $goods_type
 * @property string $goods_id
 * @property string $image
 * @property string $title
 * @property string $price
 * @property string $amount
 * @property string $vendor_id
 * @property string $tracking_number
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model filter(\App\Filters\AbstractFilter $filters)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCart newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCart newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\OrderCart query()
 * @mixin \Eloquent
 * @property int $order_id
 * @property int $user_id
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $goods
 */
class OrderCart extends Model implements GoodsContract, AuditableInterface, NeedsAuditWhenCreated
{
    use SoftDeletes, ProductGoodsTrait, AuditableTrait;

    protected $table = 'order_cart';
    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        self::creating(function (self $cart) {
            if (!$cart->user_id) {
                $cart->user_id = $cart->order->user_id;
            }
        });

        self::saving(function ($cart) {
            event('order.cart.changing', $cart);
        });

        self::saved(function ($cart) {
            event('order.cart.changed', $cart);
        });

        self::deleted(function ($cart) {
            event('order.cart.changed', $cart);
        });
    }

    public function goods(): MorphTo
    {
        return $this->morphTo('goods');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
