<?php

namespace App\Support\Calendar\Traits;

use Carbon\Carbon;

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
     * @param mixed  $timestamp
     * @param string $format
     *
     * @return DateTime
     */
    protected function createDateTime($datetime = null)
    {
        return ($datetime ? Carbon::parse($datetime) : Carbon::now())->format($this->datetime_format);
    }
}
