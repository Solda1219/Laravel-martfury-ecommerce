<?php

namespace Botble\Ecommerce\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Traits\EnumCastable;
use Botble\Ecommerce\Enums\OrderStatusEnum;
use Botble\Ecommerce\Enums\ShippingMethodEnum;
use Botble\Ecommerce\Repositories\Interfaces\ShipmentInterface;
use Botble\Payment\Models\Payment;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OrderHelper;

class Order extends BaseModel
{
    use EnumCastable;

    /**
     * @var string
     */
    protected $table = 'ec_orders';

    /**
     * @var array
     */
    protected $fillable = [
        'status',
        'user_id',
        'amount',
        'currency_id',
        'tax_amount',
        'shipping_method',
        'shipping_option',
        'shipping_amount',
        'description',
        'coupon_code',
        'discount_amount',
        'sub_total',
        'is_confirmed',
        'discount_description',
        'is_finished',
        'token',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'status'          => OrderStatusEnum::class,
        'shipping_method' => ShippingMethodEnum::class,
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected static function boot()
    {
        parent::boot();

        self::deleting(function (Order $order) {
            app(ShipmentInterface::class)->deleteBy(['order_id' => $order->id]);
            Shipment::where('order_id', $order->id)->delete();
            OrderHistory::where('order_id', $order->id)->delete();
            OrderProduct::where('order_id', $order->id)->delete();
            OrderAddress::where('order_id', $order->id)->delete();
            app(PaymentInterface::class)->deleteBy(['order_id' => $order->id]);
        });
    }

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Customer::class, 'user_id', 'id')->withDefault();
    }

    /**
     * @return mixed
     */
    public function getUserNameAttribute()
    {
        return $this->user->name;
    }

    /**
     * @return HasOne
     */
    public function address()
    {
        return $this->hasOne(OrderAddress::class, 'order_id')->withDefault();
    }

    /**
     * @return HasMany
     */
    public function products()
    {
        return $this->hasMany(OrderProduct::class, 'order_id')->with(['product']);
    }

    /**
     * @return HasMany
     */
    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id')->with(['user', 'order']);
    }

    /**
     * @return array|null|string
     */
    public function getShippingMethodNameAttribute()
    {
        return OrderHelper::getShippingMethod(
            $this->attributes['shipping_method'],
            $this->attributes['shipping_option']
        );
    }

    /**
     * @return HasOne
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class)->withDefault();
    }

    /**
     * @return BelongsTo
     */
    public function payment()
    {
        return $this->belongsTo(Payment::class, 'payment_id')->withDefault();
    }

    /**
     * @return bool
     */
    public function canBeCanceled()
    {
        return in_array($this->status, [OrderStatusEnum::PENDING, OrderStatusEnum::PROCESSING]);
    }
}
