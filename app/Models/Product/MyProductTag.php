<?php

namespace App\Models\Product;

use App\Models\User;
use App\SuperClass\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Product\MyProductTag
 *
 * @property int $id
 * @property int $user_id
 * @property int $my_product_id
 * @property string $title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product\MyProduct $myProduct
 */
class MyProductTag extends Model
{
    protected $table = 'my_product_tags';

    protected $guarded = [];

    protected $fillable = [
        'title',
        'user_id',
        'my_product_id',
    ];


    public function myProduct(): BelongsTo
    {
        return $this->belongsTo(MyProduct::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
