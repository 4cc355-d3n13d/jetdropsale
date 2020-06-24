<?php

namespace App\Models;

use App\Enums\OrderStatusType;
use App\Enums\ShopifyStatusType;
use App\Exceptions\OrderStatusException;
use App\Models\Product\MyProduct;
use App\Models\Product\Product;
use App\Models\Shopify\Shop;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;
use ScoutElastic\Searchable;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property int $shop_id
 * @property int $user_id
 * @property int $status
 * @property float|null $price
 * @property int|null $invoice_id
 * @property string|null $origin
 * @property int|null $origin_id
 * @property string|null origin_name
 * @property int|null $origin_status
 * @property string|null $tracking_number
 * @property array $shipping_address
 * @property array $billing_address
 * @property object $product_variants Deprecated
 * @property string|null $vendor_id
 * @property string|null $source_json
 * @property string|null $notes
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property \Carbon\Carbon|null $auto_confirm_at
 * @method static bool|null restore() For soft-deletes only
 * @method static bool|null forceDelete() For soft-deletes only
 * @method static \Illuminate\Database\Eloquent\Builder|static onlyTrashed() For soft-deletes only
 * @method static \Illuminate\Database\Eloquent\Builder|static withTrashed() For soft-deletes only
 * @method static \Illuminate\Database\Eloquent\Builder|static withoutTrashed() For soft-deletes only
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\OrderCart[] $cart
 * @property-read \App\Models\Invoice|null $invoice
 * @property-read \App\Models\Shopify\Shop $shop
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Order extends Model implements AuditableInterface, NeedsAuditWhenCreated
{
    use AuditableTrait;
    use Searchable;

    protected $indexConfigurator = OrderIndexConfigurator::class;

    protected $mapping = [
        'properties' => [
            'status'           => ['type' => 'keyword'],
            'tracking_number'  => ['type' => 'keyword'],
            'origin_name'      => ['type' => 'keyword'],
            'notes'            => ['type' => 'text', 'analyzer' => 'my_analyzer'],
            'user_email'       => ['type' => 'text', 'analyzer' => 'my_email_analyzer'],
        ]
    ];

    public const SHIPPING_PRICE = 2.90;

    protected $table = 'orders';

    protected $appends = ['user_email'];

    protected $fillable = [
        'user_id',
        'shop_id',
        'origin_id',
        'origin_name',
        'origin_status',
        'auto_confirm_at',
        'vendor_id',
        'status',
        'notes',
        'price',
        'user_email',
    ];

    public function toSearchableArray()
    {
        return array_merge($this->toArray(), [
            'user_email' => $this->getUserEmailAttribute()
        ]);
    }

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'product_variants' => 'object',
        'shipping_address' => 'array',
        'billing_address' => 'array',
        'status' => 'integer',
        'auto_confirm_at' => 'datetime'
    ];

    public static function boot()
    {
        parent::boot();
        self::creating(function (self $model) {
            $model->reCalcPrice();
        });
        self::created(function (self $model) {
            if ($model->origin_status != ShopifyStatusType::MIGRATED) {
                if (! $model->status) {
                    $model->changeStatus(OrderStatusType::HOLD, true, true);
                }
                $model->addToCart(ShipGoods::first());
            }
        });

        self::updated(function (Order $order) {
            if ($order->isDirty('status')) {
                $order->syncOriginalAttribute('status');
                event('order.status.' . strtolower(OrderStatusType::getKey($order->status)), $order);
            }
        });
        self::deleting(function (Order $model) {
            $model->cart()->delete();
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * todo remove ships hardcode
     */
    public function reCalcPrice()
    {
        $this->price = $this->cart->sum(function (OrderCart $cart) {
            return $cart->price * $cart->amount;
        });
    }

    /**
     * @param int $newStatusCode
     * @param bool $instantSave Save should not be called during update listener work
     * @param bool $force force change status
     * @return bool
     * @throws OrderStatusException
     */
    public function changeStatus(int $newStatusCode, bool $instantSave = true, bool $force = false): bool
    {
        $newStatus = OrderStatusType::getKey($newStatusCode);
        if (! $force) {
            $canBe = OrderStatusType::getAvailableManualTransitions($this->status);
            if (! in_array($newStatus, $canBe) && $this->status !== $newStatusCode) {
                throw new OrderStatusException('Wrong status transition requested ' . $this->status . "->" . $newStatusCode);
            }
        }

        switch ($newStatusCode) {
            case OrderStatusType::HOLD:
                $this->auto_confirm_at = now()->addMinutes($this->user->setting('order_hold_time'));
                break;
            case OrderStatusType::PAUSED:
            case OrderStatusType::CONFIRMED:
            case OrderStatusType::CANCELLED:
            case OrderStatusType::PENDING:
                $this->auto_confirm_at = null;
                break;
        }

        $this->status = $newStatusCode;
        $return = $instantSave ? $this->save() : true;

        return $return;
    }

    public function myProducts() : BelongsToMany
    {
        return $this->belongsToMany(MyProduct::class);
    }

    public function cart(): HasMany
    {
        return $this->hasMany(OrderCart::class);
    }

    /**
     * @param GoodsContract | Product $model
     * @param bool $force нужна для миграции - мы мигрировали без вариантов
     * @return bool
     */
    public function addToCart(GoodsContract $model, $force = false): bool
    {
        if ($model instanceof OrderCart && $force) {
            tap(OrderCart::firstOrCreate([
                'title' => $model->getTitle(),
                'price' => $model->getPrice(),
                'amount'=> $model->getAmount(),
                'user_id' => $this->user_id,
                'order_id' => $this->id
            ]), function (OrderCart $cart) use ($model) {
                if ($cart->wasRecentlyCreated) {
                    $cart->update(['image'=>$model->getImage()]);
                }
            });
            return true;
        }
        if (! $this->cart()->where([
            'goods_type' => get_class($model),
            'goods_id'  => $model->getKey()
        ])->exists()) {
            $cart = new OrderCart();
            $cart->title = $model->getTitle();
            $cart->image = $model->getImage();
            $cart->price = $model->getPrice();
            $cart->amount = $model->getAmount();
            $cart->user_id = $this->user_id;
            $cart->goods()->associate($model);
            $this->cart()->save($cart);
            $this->refresh();
            $this->reCalcPrice();
            return $this->save();
        } elseif ($force) {
            tap(OrderCart::firstOrCreate([
                'title' => $model->getTitle(),
                'price' => $model->getPrice(),
                'amount'=> $model->getAmount(),
                'user_id' => $this->user_id,
                'order_id' => $this->id
            ]), function (OrderCart $cart) use ($model) {
                if ($cart->wasRecentlyCreated) {
                    $cart->update(['image'=>$model->getImage()]);
                }
            });
        }
        
        return false;
    }

    /**
     * @return mixed
     */
    public function getUserEmailAttribute()
    {
        return $this->user->email;
    }
}
