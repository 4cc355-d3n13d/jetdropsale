<?php

namespace App\Models\User;

use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Collection;

/**
 * App\Models\User\Setting
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $value
 * @property string $description
 * @property-read \App\Models\User $user
 * @mixin \Eloquent
 */
class Setting extends Model
{
    protected $table = 'user_settings';

    protected $fillable = [
        'user_id',
        'key',
        'value',
        'description',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    public static function byUser(int $userId): Collection
    {
        /** @var EloquentCollection|self[] $settings */
        $settings = self::where('user_id', $userId)->get();

        return self::defaultSettings()->mapWithKeys(function ($item, $key) use ($settings) {
            if ($setting = $settings->firstWhere('key', $key)) {
                return [$setting->key => $setting->value];
            }
            return [$key=>$item['value']];
        });
    }

    public static function defaultSettings(): Collection
    {
        return collect([
            'gpr_rate' => [
                'value' => '2',
                'rule' => 'numeric'
            ],
            'gpr_type' => [
                'value' => 'm',
                'rule'  => 'in:f,m'
            ],
            'order_hold_time' => [
                'value' => '120',
                'rule' => 'integer|min:0|max:14400'
            ]
        ]);
    }
}
