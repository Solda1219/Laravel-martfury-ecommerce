<?php

namespace Botble\Analytics\Exceptions;

use DateTime;
use Exception;

class InvalidPeriod extends Exception
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return static
     */
    public static function startDateCannotBeAfterEndDate(DateTime $startDate, DateTime $endDate)
    {
        return new static(trans('plugins/analytics::analytics.start_date_can_not_before_end_date', [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date'   => $endDate->format('Y-m-d'),
        ]));
    }
}
