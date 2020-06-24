<?php

namespace App\Models;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\Card
 *
 * @property int $id Internal card identificator
 * @property int $user_id Card owner user identificator for relation
 * @property int $primary If it is the user`s primary card
 * @property string $billing_reference Source reference for payment gateways
 * @property string $brand Card brand
 * @property int $last4 Last 4 digits of card number
 * @property int $exp_month Card expire month
 * @property int $exp_year Card expire year
 * @property mixed|null $data Full card information data received from payment gateway
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Card extends Model implements AuditableInterface
{
    use AuditableTrait;
    use SoftDeletes;

    protected $table = 'user_cards';
    protected $guarded = [];
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Get the user that owns the card.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
