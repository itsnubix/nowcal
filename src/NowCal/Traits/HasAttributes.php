<?php

namespace NowCal\Traits;

trait HasAttributes
{
    /**
     * The required fields for the VCalendar.
     *
     * @var array
     */
    protected $calendar = [
        'prodid',
        'version',
    ];

    /**
     * The required fields for the VEvent.
     *
     * @var array
     */
    protected $event = [
        'uid',
        'created',
        'stamp',
    ];

    /**
     * The fields that are allowed to be set by the user.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8
     *
     * @todo support additional props
     *
     * @var array
     */
    protected $allowed = [
        'start',
        'end',
        'summary',
        'location',
        'duration',
    ];

    /**
     * The fields required by the iCalendar specification.
     *
     * @var array
     */
    protected $required = [
        'prodid',
        'version',
        'uid',
        'created',
        'stamp',
        'start',
    ];

    /**
     * This property specifies when the calendar component begins.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.4
     *
     * @var string
     */
    public $start;

    /**
     * This property specifies the date and time that a calendar
     * component ends.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.2
     *
     * @var string
     */
    public $end;

    /**
     * This property defines a short summary or subject for the
     * calendar component.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.12
     *
     * @var string
     */
    public $summary;

    /**
     * This property defines the intended venue for the activity
     * defined by a calendar component.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.7
     *
     * @var string
     */
    public $location;

    /**
     * Check if the key is allowed to be set.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function allowed(string $key): bool
    {
        return in_array($key, $this->allowed);
    }

    /**
     * Check if the key is required.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function required(string $key): bool
    {
        return in_array($key, $this->required);
    }

    /**
     * Set the event's start date.
     *
     * @param mixed $timestamp
     *
     * @return \NowCal\NowCal
     */
    public function start($datetime): self
    {
        $this->set('start', $datetime);

        return $this;
    }

    /**
     * Set the event's end date.
     *
     * @param mixed $timestamp
     *
     * @return \NowCal\NowCal
     */
    public function end($datetime): self
    {
        $this->set('end', $datetime);

        return $this;
    }

    /**
     * Set the event's summary.
     *
     * @param mixed $summary
     *
     * @return \NowCal\NowCal
     */
    public function summary($summary): self
    {
        $this->set('summary', $summary);

        return $this;
    }

    /**
     * Set the event's location.
     *
     * @param mixed $location
     *
     * @return \NowCal\NowCal
     */
    public function location($location): self
    {
        $this->set('location', $location);

        return $this;
    }

    /**
     * Set the event's duration.
     *
     * @param mixed $location'
     *
     * @return \NowCal\NowCal
     */
    public function duration($duration): self
    {
        $this->set('duration', $duration);

        return $this;
    }
}
