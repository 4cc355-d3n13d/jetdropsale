<?php declare(strict_types=1);

namespace App\Models\User;

use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\User\SocialAccount
 *
 * @property int $id
 * @property int $user_id
 * @property string $provider_user_id
 * @property string $provider
 * @property array $provider_user_data
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class SocialAccount extends Model
{
    protected $table = 'user_social_accounts';

    protected $fillable = [
        'user_id',
        'provider_user_id',
        'provider',
        'provider_user_data',
    ];

    protected $casts = [
        'provider_user_data' => 'json',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
