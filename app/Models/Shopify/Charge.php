<?php

namespace App\Models\Shopify;

use App\Models\NeedsAuditWhenCreated;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

class Charge extends Model implements AuditableInterface, NeedsAuditWhenCreated
{
    use SoftDeletes, AuditableTrait;

    protected $table = 'shopify_charges';

    protected $fillable = [
        'charge_id',
        'status',
        'billing_on',
        'test',
        'activated_on',
        'trial_ends_on',
        'cancelled_on',
        'trial_days',
        'shop_id',
        'price',
        'name',
    ];

    protected $dispatchesEvents = [];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public static function createEarlyAceessFreeCharge(Shop $shop): Charge
    {
        return Charge::create(
            [
                'name' => 'Dropwow early register',
                'price' => '0.00',
                'status' => 'active',
                'activated_on' => Date('Y-m-d'),
                'shop_id' => $shop->id,
                'test' => false,
            ]
        );
    }
}
