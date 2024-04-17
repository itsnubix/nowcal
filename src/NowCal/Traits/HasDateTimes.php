<?php

namespace NowCal\Traits;

use DateInterval;
use DateTime;

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
    protected function createDateTime($datetime = 'now'): string
    {
        return (new DateTime($datetime ?? 'now'))
            ->format($this->datetime_format);
    }

    /**
     * Parses and creates an ISO 8601.2004 interval.
     *
     * @param mixed $interval
     *
     * @return string
     */
    protected function createInterval(string $interval = '0s'): string
    {
        return $this->transformDateIntervalToString($this->createDateIntervalFromString($interval));
    }

    public function createDateIntervalFromString(string $string)
    {
        $years = 0;
        $months = 0;
        $days = 0;
        $hours = 0;
        $minutes = 0;
        $seconds = 0;

        foreach (explode(' ', $string) as $v) {
            switch (substr($v, -1)) {
                case 'y':
                    $years += intval($v);
                    break;
                case 'M':
                    $months += intval($v);
                    break;
                case 'w':
                    $days += intval($v) * 7;
                    break;
                case 'd':
                    $days += intval($v);
                    break;
                case 'h':
                    $hours += intval($v);
                    break;
                case 'm':
                    $minutes += intval($v);
                    break;
                case 's':
                    $seconds += intval($v);
                    break;
            }
        }

        return new DateInterval(
            'P' . // Period
            $years . 'Y' .
            $months . 'M' .
            $days . 'D' .
            'T' . // Time
            $hours . 'H' .
            $minutes . 'M' .
            $seconds . 'S',
        );
    }

    protected function transformDateIntervalToString(DateInterval $interval)
    {
        // Reading all non-zero date parts.
        $date = array_filter(array(
            'Y' => $interval->y,
            'M' => $interval->m,
            'D' => $interval->d,
        ));

        // Reading all non-zero time parts.
        $time = array_filter(array(
            'H' => $interval->h,
            'M' => $interval->i,
            'S' => $interval->s,
        ));

        $specString = 'P';

        // Adding each part to the spec-string.
        foreach ($date as $key => $value) {
            $specString .= $value . $key;
        }
        if (count($time) > 0) {
            $specString .= 'T';
            foreach ($time as $key => $value) {
                $specString .= $value . $key;
            }
        }

        return $specString;
    }
}
