<?php

namespace NowCal;

use DateInterval;
use DateTime;

class NowCal
{
    /**
     * The required fields for the VCalendar.
     *
     * @var array
     */
    public const VCALENDAR = [
        'prodid',
        'version',
    ];

    /**
     * The required fields for the VEvent.
     *
     * @var array
     */
    public const VEVENT = [
        'uid',
        'created',
        'stamp',
    ];

    /**
     * The fields that are allowed to be set by the user.
     *
     * @var array
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8
     */
    public const ALLOWED = [
        'start',
        'end',
        'summary',
        'location',
        'duration',
        'timezone',
    ];

    /**
     * The fields required by the iCalendar specification.
     *
     * @var array
     */
    public const REQUIRED = [
        'prodid',
        'version',
        'uid',
        'created',
        'stamp',
        'start',
    ];

    /**
     * All the properties that need to be cast.
     *
     * @var array
     */
    public const CASTS = [
        'duration' => 'interval',
        'created' => 'datetime',
        'stamp' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * CRLF return.
     *
     * @var string
     */
    public const CRLF = "\r\n";

    /**
     * The format to use for datetimes.
     *
     * @var string
     */
    public const DATETIME_FORMAT = 'Ymd\THis\Z';

    /**
     * iCalendar Product Identifier.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.3
     */
    private string $prodid = '-//itsnubix//NowCal//EN';

    /**
     * Specifies the minimum iCalendar specification that is required
     * in order to interpret the iCalendar object.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.4
     */
    protected string $version = '2.0';

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
     * This property defines the timezone for the calendar meeting
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5545#section-3.6.5
     */
    public ?string $timezone = null;

    /**
     * The .ics raw array output.
     */
    protected array $output = [];

    /**
     * Instantiate the NowCal class.
     */
    public function __construct(array $params = [])
    {
        $this->merge($params);
    }

    /**
     * Magic method for getting computed properties.
     */
    public function __get(string $key)
    {
        if (method_exists(self::class, $method = 'get' . $this->convertStringToPascalCase($key) . 'Attribute')) {
            return $this->{$method}();
        }
    }

    /**
     * Pass the props into the class and create a new instance.
     */
    public static function create(array $props = []): self
    {
        return new self($props);
    }

    /**
     * Create an ICS array and output as raw array.
     */
    public static function raw(array $props = []): array
    {
        return self::create($props)->raw;
    }

    /**
     * Return the plain text version of the invite.
     */
    public static function plain(array $props = []): string
    {
        return self::create($props)->plain;
    }

    /**
     * Return a path to a .ics tempfile.
     */
    public static function file(array $props = []): string
    {
        return self::create($props)->file;
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
     * Set the event's duration using a DateInterval.
     */
    public function timezone(string|\Closure $timezone): self
    {
        $this->set('timezone', $timezone);

        return $this;
    }

    /**
     * Check if the key is allowed to be set.
     */
    protected function allowed(string $key): bool
    {
        return in_array($key, static::ALLOWED);
    }

    /**
     * Check if the key is required.
     */
    protected function required(string $key): bool
    {
        return in_array($key, static::REQUIRED);
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

    /**
     * Cast the specified value as the provided type.
     */
    protected function cast(mixed $value, ?string $as = null): string
    {
        switch ($as) {
            case 'datetime':
                return $this->castDateTime($value);
            case 'interval':
                return $this->castInterval($value);
            default:
                return $value;
        }
    }

    /**
     * Cast the specified value as a datetime.
     */
    protected function castDateTime($value): string
    {
        return $this->createDateTime($value);
    }

    /**
     * Cast the specified value as an ISO 8601.2004 interval.
     */
    protected function castInterval(mixed $value): string
    {
        return $this->createInterval($value);
    }

    /**
     * Check if the specified key has a caster.
     */
    protected function hasCaster(string $key): bool
    {
        return array_key_exists($key, static::CASTS);
    }

    /**
     * Compile the event's raw output.
     */
    protected function compile(): self
    {
        $this->output = [];

        $this->beginCalendar();
        $this->createEvent();
        $this->endCalendar();

        return $this;
    }

    /**
     * Open the VCalendar tag and add necessary props.
     */
    protected function beginCalendar(): void
    {
        $this->output[] = 'BEGIN:VCALENDAR';

        foreach (static::VCALENDAR as $key) {
            $this->output[] = $this->getParameter($key);
        }
    }

    /**
     * Close the VCalendar tag.
     */
    protected function endCalendar(): void
    {
        $this->output[] = 'END:VCALENDAR';
    }

    /**
     * Create the VEvent and include all its props.
     */
    protected function createEvent(): void
    {
        $this->output[] = 'BEGIN:VEVENT';

        foreach ($this->event_parameters as $key) {
            $this->output[] = $this->getParameter($key);
        }

        $this->output[] = 'END:VEVENT';
    }

    /**
     * Get the provided parameter from the ICS spec. If not
     * included in the spec then fail. If not provided but
     * required then throw exception.
     *
     * @throws Exception
     */
    protected function getParameter(string $key): string
    {
        if ($this->has($key)) {
            return $this->getParameterKey($key) . ':' . $this->getParameterValue($key);
        }

        if ($this->required($key)) {
            throw new \Exception('Key "' . $key . '" is not set but is required');
        }
    }

    /**
     * Returns the iCalendar param key.
     */
    protected function getParameterKey(string $name): string
    {
        $key = strtoupper($name);

        switch ($name) {
            case 'start':
            case 'end':
            case 'stamp':
                return 'DT' . $key;
            default:
                return $key;
        }
    }

    /**
     * Return the associated value for the supplied iCal param.
     */
    protected function getParameterValue(string $key): string
    {
        if ($this->has($key)) {
            if ($this->hasCaster($key)) {
                return $this->cast($this->{$key}, static::CASTS[$key]);
            }

            return $this->{$key};
        }

        return null;
    }

    /**
     * Concatenate the invite's parameters.
     */
    protected function getEventParametersAttribute(): array
    {
        return array_filter(
            array_merge(static::VEVENT, static::ALLOWED),
            function ($key) {
                return $this->has($key);
            },
        );
    }

    /**
     * Create and return a UUID.
     */
    protected function getUidAttribute(): string
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Create and return a timestamp.
     */
    protected function getStampAttribute(): string
    {
        return $this->createDateTime();
    }

    /**
     * Create and return the created at timestamp.
     */
    protected function getCreatedAttribute(): string
    {
        return $this->createDateTime();
    }

    /**
     * Return the invite's raw output array.
     */
    public function getRawAttribute(): array
    {
        $this->compile();

        return $this->output;
    }

    /**
     * Return the invite's data as plain text.
     */
    public function getPlainAttribute(): string
    {
        $this->compile();

        return implode(self::CRLF, $this->output);
    }

    /**
     * Creates a tempfile and returns its path.
     */
    public function getFileAttribute(): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'event_') . '.ics';
        file_put_contents($filename, $this->plain . self::CRLF);

        return $filename;
    }

    /**
     * Return the file name.
     */
    protected function getFileNameAttribute(): string
    {
        return $this->uid . '_event.ics';
    }

    /**
     * Converts a string into PascalCase.
     */
    protected function convertStringToPascalCase(string $string): string
    {
        $words = explode(' ', str_replace(['-', '_'], ' ', $string));
        $output = array_map(fn($word) => ucfirst($word), $words);

        return implode($output);
    }

    /**
     * Parses and creates a datetime.
     */
    protected function createDateTime(string|DateTime|\Closure $datetime = 'now'): string
    {
        return (new DateTime($datetime ?? 'now'))
            ->format(static::DATETIME_FORMAT);
    }

    /**
     * Parses and creates an ISO 8601.2004 interval.
     */
    protected function createInterval(string $interval = '0s'): string
    {
        return $this->transformDateIntervalToString($this->createDateIntervalFromString($interval));
    }

    /**
     * Convert a string to a DateInterval
     *
     * @example '1y 2M 3w 4d 5h 6m 7s'
     */
    protected function createDateIntervalFromString(string $string): DateInterval
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

    protected function transformDateIntervalToString(DateInterval $interval): string
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
