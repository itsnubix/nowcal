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
     * Parses and creates an ISO 8601.2004 duration.
     *
     * @param mixed $duration
     *
     * @return string
     */
    protected function createDuration($duration = null): string
    {
        return CarbonInterval::fromString($duration ?? '0s')
            ->spec();
    }
}
