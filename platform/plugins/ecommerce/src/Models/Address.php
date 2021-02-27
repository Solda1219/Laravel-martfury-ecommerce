<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Helper;

class Address extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'ec_customer_addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'phone',
        'email',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'customer_id',
        'is_default',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    /**
     * @return string
     */
    public function getCountryNameAttribute()
    {
        return Helper::getCountryNameByCode($this->country);
    }
}
