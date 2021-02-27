<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends BaseModel
{
    use EnumCastable;

    /**
     * @var string
     */
    protected $table = 'ec_reviews';

    /**
     * @var array
     */
    protected $fillable = [
        'product_id',
        'customer_id',
        'star',
        'comment',
        'status',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id')->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /**
     * @return string
     */
    public function getProductNameAttribute()
    {
        return $this->product->name;
    }

    /**
     * @return string
     */
    public function getUserNameAttribute()
    {
        return $this->user->name;
    }
}
