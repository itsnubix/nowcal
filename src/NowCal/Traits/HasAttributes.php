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
     */
    public ?string $start = null;

    /**
     * This property specifies the date and time that a calendar
     * component ends.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.2.2
     */
    public ?string $end = null;

    /**
     * The duration is a interval coded string (ie, P28DT6H42M12S = 28 days, 6 hours, 42 minutes, 12 seconds)
     * that specifies the duration of the event.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5545#section-3.3.6
     */
    public ?string $duration = null;

    /**
     * This property defines a short summary or subject for the
     * calendar component.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.12
     */
    public ?string $summary = null;

    /**
     * This property defines the intended venue for the activity
     * defined by a calendar component.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8.1.7
     */
    public ?string $location = null;

    /**
     * Check if the key is allowed to be set.
     */
    protected function allowed(string $key): bool
    {
        return in_array($key, $this->allowed);
    }

    /**
     * Check if the key is required.
     */
    protected function required(string $key): bool
    {
        return in_array($key, $this->required);
    }

    /**
     * Set the event's start date.
     */
    public function start(string|\Closure|\DateTime $datetime): self
    {
        $this->set('start', $datetime);

        return $this;
    }

    /**
     * Set the event's end date.
     */
    public function end(string|\Closure|\DateTime $datetime): self
    {
        if (!$this->has('duration')) {
            $this->set('end', $datetime);
        }

        return $this;
    }

    /**
     * Set the event's summary.
     */
    public function summary(string|\Closure $summary): self
    {
        $this->set('summary', $summary);

        return $this;
    }

    /**
     * Set the event's location.
     */
    public function location(string|\Closure $location): self
    {
        $this->set('location', $location);

        return $this;
    }

    /**
     * Set the event's duration using a DateInterval.
     */
    public function duration(string|\Closure $duration): self
    {
        if (!$this->has('end')) {
            $this->set('duration', $duration);
        }

        return $this;
    }

    /**
     * Get the class' property.
     */
    protected function get(string $key): mixed
    {
        if ($this->allowed($key)) {
            return $this->{$key};
        }

        return null;
    }

    /**
     * Set the class' properties.
     */
    protected function set(string|array $key, $val = null): void
    {
        if (is_array($key)) {
            $this->merge($key);
        } else {
            if (is_callable($val)) {
                $val = $val();
            }

            if ($this->allowed($key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * Check if the class has a key.
     */
    protected function has(string $key): bool
    {
        return isset($this->{$key}) && !is_null($this->{$key});
    }

    /**
     * Merge multiple properties.
     */
    protected function merge(array $props)
    {
        foreach ($props as $key => $val) {
            $this->set($key, $val);
        }
    }
}
