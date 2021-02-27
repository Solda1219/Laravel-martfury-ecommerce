<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;

class ProductAttribute extends BaseModel
{
    use EnumCastable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ec_product_attributes';

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'color',
        'status',
        'order',
        'attribute_set_id',
        'image',
        'is_default',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    /**
     * @param int $value
     * @return int
     */
    public function getAttributeSetIdAttribute($value)
    {
        return (int)$value;
    }

    /**
     * @param int $value
     * @return int
     */
    public function getGroupIdAttribute($value)
    {
        return (int)$value;
    }
}
