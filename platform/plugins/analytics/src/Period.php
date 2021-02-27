<?php

namespace Botble\Analytics;

use Carbon\Carbon;
use DateTime;
use Botble\Analytics\Exceptions\InvalidPeriod;

class Period
{
    /**
     * @var DateTime
     */
    public $startDate;

    /**
     * @var DateTime
     */
    public $endDate;

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return Period
     * @throws InvalidPeriod
     */
    public static function create(DateTime $startDate, DateTime $endDate): Period
    {
        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfDays
     * @return Period
     * @throws InvalidPeriod
     */
    public static function days(int $numberOfDays): Period
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subDays($numberOfDays)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfMonths
     * @return Period
     * @throws InvalidPeriod
     */
    public static function months(int $numberOfMonths): Period
    {
        $endDate = Carbon::today();

            $startDate = Carbon::today()->subMonths($numberOfMonths)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * @param int $numberOfYears
     * @return Period
     * @throws InvalidPeriod
     */
    public static function years(int $numberOfYears): Period
    {
        $endDate = Carbon::today();

        $startDate = Carbon::today()->subYears($numberOfYears)->startOfDay();

        return new static($startDate, $endDate);
    }

    /**
     * Period constructor.
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @throws InvalidPeriod
     */
    public function __construct(DateTime $startDate, DateTime $endDate)
    {
        if ($startDate > $endDate) {
            throw InvalidPeriod::startDateCannotBeAfterEndDate($startDate, $endDate);
        }

        $this->startDate = $startDate;

        $this->endDate = $endDate;
    }
}
