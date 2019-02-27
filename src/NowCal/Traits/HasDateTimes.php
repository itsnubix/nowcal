<?php

namespace NowCal\Traits;

use Carbon\Carbon;
use Carbon\CarbonInterval;

trait HasDateTimes
{
    /**
     * The format to use for datetimes.
     *
     * @var string
     */
    protected $datetime_format = 'Ymd\THis\Z';

    /**
     * Parses and creates a datetime.
     *
     * @param mixed $timestamp
     *
     * @return string
     */
    protected function createDateTime($datetime = null): string
    {
        return Carbon::parse($datetime ?? 'now')
            ->format($this->datetime_format);
    }

    /**
     * Parses and creates an ISO 8601.2004 interval.
     *
     * @param mixed $interval
     *
     * @return string
     */
    protected function createInterval($interval = null): string
    {
        return CarbonInterval::fromString($interval ?? '0s')
            ->spec();
    }
}
