<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\Audit
 *
 * @property int $id
 * @property string $user_type
 * @property int $user_id
 * @property string $event
 * @property string $auditable_type
 * @property string $auditable_id
 * @property string $old_values
 * @property string $new_values
 * @property string $url
 * @property string $ip_address
 * @property string $user_agent
 * @property string $tags
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Audit extends Model
{
    protected $table = 'audits';

    protected $guarded = [];
}
