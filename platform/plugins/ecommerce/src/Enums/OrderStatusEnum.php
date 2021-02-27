<?php

namespace Botble\Ecommerce\Enums;

use Botble\Base\Supports\Enum;
use Html;

/**
 * @method static OrderStatusEnum PENDING()
 * @method static OrderStatusEnum PROCESSING()
 * @method static OrderStatusEnum DELIVERING()
 * @method static OrderStatusEnum DELIVERED()
 * @method static OrderStatusEnum COMPLETED()
 * @method static OrderStatusEnum CANCELED()
 */
class OrderStatusEnum extends Enum
{
    public const PENDING = 'pending';
    public const PROCESSING = 'processing';
    public const DELIVERING = 'delivering';
    public const DELIVERED = 'delivered';
    public const COMPLETED = 'completed';
    public const CANCELED = 'canceled';

    /**
     * @var string
     */
    public static $langPath = 'plugins/ecommerce::order.statuses';

    /**
     * @return string
     */
    public function toHtml()
    {
        switch ($this->value) {
            case self::PENDING:
                return Html::tag('span', self::PENDING()->label(), ['class' => 'label-warning status-label'])
                    ->toHtml();
            case self::PROCESSING:
                return Html::tag('span', self::PROCESSING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::DELIVERING:
                return Html::tag('span', self::DELIVERING()->label(), ['class' => 'label-info status-label'])
                    ->toHtml();
            case self::DELIVERED:
                return Html::tag('span', self::DELIVERED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::COMPLETED:
                return Html::tag('span', self::COMPLETED()->label(), ['class' => 'label-success status-label'])
                    ->toHtml();
            case self::CANCELED:
                return Html::tag('span', self::CANCELED()->label(), ['class' => 'label-danger status-label'])
                    ->toHtml();
            default:
                return parent::toHtml();
        }
    }
}
