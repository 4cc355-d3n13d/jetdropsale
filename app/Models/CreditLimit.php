<?php

namespace App\Models;

use App\SuperClass\Model;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable as AuditableInterface;

/**
 * App\Models\CreditLimit
 *
 * @property int $limit
 * @property-read \Illuminate\Database\Eloquent\Collection|\OwenIt\Auditing\Models\Audit[] $audits
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model last($column = 'created_at')
 * @method static \Illuminate\Database\Eloquent\Builder|\App\SuperClass\Model lastOrFail($column = 'created_at')
 * @mixin \Eloquent
 */
class CreditLimit extends Model implements AuditableInterface
{
    use AuditableTrait;

    protected $primaryKey = 'limit';
    public $incrementing = false;
    public $timestamps = false;
}
