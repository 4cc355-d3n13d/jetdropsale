<?php

namespace App\Models;

use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\UserBalanceLog
 *
 * @property int $id
 * @property int $user_id
 * @property float $balance_before
 * @property float $balance_diff
 * @property float $balance_after
 * @property string $initiator_type
 * @property int $initiator_id
 * @property string $description
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class UserBalanceHistory extends Model implements AuditableInterface, NeedsAuditWhenCreated
{
    use AuditableTrait;


    protected static $unguarded = true;

    protected $table = 'user_balance_history';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];


    public function initiator(): MorphTo
    {
        return $this->morphTo('initiator');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
