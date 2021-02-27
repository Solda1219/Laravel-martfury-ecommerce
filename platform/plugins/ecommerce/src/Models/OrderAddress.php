<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Supports\Avatar;
use Botble\Base\Supports\Helper;

class OrderAddress extends BaseModel
{

    /**
     * @var string
     */
    protected $table = 'ec_order_addresses';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'address',
        'zip_code',
        'order_id',
    ];

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getCountryNameAttribute()
    {
        return Helper::getCountryNameByCode($this->country);
    }

    /**
     * @return string
     */
    public function getAvatarUrlAttribute()
    {
        return (string)(new Avatar)->create($this->name)->toBase64();
    }
}
