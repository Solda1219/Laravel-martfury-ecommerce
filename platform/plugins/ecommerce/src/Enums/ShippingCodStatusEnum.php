<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;

/**
 * @method static ShippingCodStatusEnum PENDING()
 * @method static ShippingCodStatusEnum COMPLETED()
 */
class ShippingCodStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const COMPLETED = 'completed';

    /**
     * @var string
     */
    public static $langPath = 'plugins/ecommerce::shipping.cod_statuses';
}
