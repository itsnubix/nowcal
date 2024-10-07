<?php

namespace NowCal;

use Closure;
use DateInterval;
use DateTime;
use DateTimeZone;
use Exception;

class NowCal
{
    /**
     * The required fields for the VCalendar.
     *
     * @var array
     */
    public const VCALENDAR = [
        'method',
        'prodid',
        'version',
    ];

    /**
     * The required fields for the VEvent.
     *
     * @var array
     */
    public const VEVENT = [
        'stamp',
        'created',
    ];

    /**
     * The fields that are allowed to be set by the user.
     *
     * @var array
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.8
     */
    public const ALLOWED = [
        'end',
        'uid',
        'start',
        'method',
        'summary',
        'location',
        'duration',
        'sequence',
        'timezone',
        'reminder'
    ];

    /**
     * The fields required by the iCalendar specification.
     *
     * @var array
     */
    public const REQUIRED = [
        'stamp',
        'start',
        'prodid',
        'created',
        'version',
    ];

    /**
     * All the properties that need to be cast.
     *
     * @var array
     */
    public const CASTS = [
        'end' => 'datetime',
        'method' => 'method',
        'stamp' => 'datetime',
        'start' => 'datetime',
        'created' => 'datetime',
        'duration' => 'interval',
        'timezone' => 'timezone',
        'reminder' => 'interval',
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
    public const DATETIME_FORMAT = 'Ymd\THis';

    /**
     * iCalendar Product Identifier.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.3
     */
    private string $prodid = '-//NowCal//EN';

    /**
     * Specifies the minimum iCalendar specification that is required
     * in order to interpret the iCalendar object.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.4
     */
    protected string $version = '2.0';

    /**
     * The globally unique identifier for the calendar component. If not
     * provided one will be generated automatically.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5545#section-3.8.4.7
     */
    public ?string $uid = null;

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
     * This property defines the iCalendar object method associated
     * with the calendar object.
     *
     * @see https://www.rfc-editor.org/rfc/rfc5546#section-3.2
     */
    public ?string $method = null;

    /**
     * This property defines the revision sequence number of the calendar
     * component within a sequence of revisions.
     *
     * @see https://datatracker.ietf.org/doc/html/rfc5545#section-3.8.7.4
     */
    public ?string $sequence = null;

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
     * The reminder for the event.
     *
     * @see https://www.rfc-editor.org/rfc/rfc5545#section-3.6.6
     */
    public ?string $reminder = null;

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

    public function uid(string|Closure $uid): self
    {
        $this->set('uid', $uid);

        return $this;
    }

    /**
     * Set the event's start date.
     */
    public function start(string|Closure|DateTime $start): self
    {
        if ($start instanceof DateTime) {
            $start = $start->format(static::DATETIME_FORMAT);
        }

        $this->set('start', $start);

        return $this;
    }

    /**
     * Set the event's end date.
     */
    public function end(string|Closure|DateTime $end): self
    {
        if ($end instanceof DateTime) {
            $end = $end->format(static::DATETIME_FORMAT);
        }

        if (!$this->has('duration')) {
            $this->set('end', $end);
        }

        return $this;
    }

    /**
     * Set the event's summary.
     */
    public function summary(string|Closure $summary): self
    {
        $this->set('summary', $summary);

        return $this;
    }

    /**
     * Set the event's location.
     */
    public function location(string|Closure $location): self
    {
        $this->set('location', $location);

        return $this;
    }

    /**
     * Set the event's duration using a DateInterval.
     */
    public function duration(string|DateInterval|Closure $duration): self
    {
        if ($duration instanceof DateInterval) {
            $duration = $this->transformDateIntervalToString($duration);
        }

        if (!$this->has('end')) {
            $this->set('duration', $duration);
        }

        return $this;
    }

    /**
     * Set the event's duration using a DateInterval.
     */
    public function timezone(string|DateTimeZone|Closure $timezone): self
    {
        if ($timezone instanceof DateTimeZone) {
            $timezone = $timezone->getName();
        }

        $this->set('timezone', $timezone);

        return $this;
    }

    /**
     * Set the event's method.
     */
    public function method(string $method): self
    {
        $this->set('method', $method);

        return $this;
    }

    /**
     * Set the event's sequence.
     */
    public function sequence(string|int $sequence): self
    {
        $this->set('sequence', (string) $sequence);

        return $this;
    }

    public function reminder(string|DateInterval|Closure $reminder): self
    {
        if ($reminder instanceof DateInterval) {
            $reminder = $this->transformDateIntervalToString($reminder);
        }

        // If the reminder is not an ISO 8601 interval then cast it.
        if (!str_contains($reminder, 'P')) {
            $reminder = $this->castInterval($reminder);
        }

        $this->set('reminder', $reminder);

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
            return;
        }

        if (!$this->allowed($key)) {
            return;
        }

        if (is_callable($val)) {
            $this->set($key, $val());
            return;
        }

        $this->{$key} = $val;
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
    protected function merge(array $props): void
    {
        foreach ($props as $key => $val) {

            if (method_exists($this, $key)) {
                // The target parameter has specific setter method, use it because it contains casting logic
                $this->$key($val);
            } else {
                $this->set($key, $val);
            }
        }
    }

    /**
     * Cast the specified value as the provided type.
     */
    protected function cast(mixed $value, ?string $as = null): string
    {
        return match ($as) {
            'upper' => $this->castUpper($value),
            'method' => $this->castMethod($value),
            'datetime' => $this->castDateTime($value),
            'interval' => $this->castInterval($value),
            'timezone' => $this->castTimezone($value),
            default => $value,
        };
    }

    /**
     * Check if the specified key has a caster.
     */
    protected function hasCaster(string $key): bool
    {
        return array_key_exists($key, static::CASTS);
    }

    /**
     * Cast the specified value to uppercase.
     */
    public function castUpper($value): string
    {
        return strtoupper($value);
    }

    public function castMethod($value): string
    {
        // Laravel has a weird glitch where if you pass 'request' in it will dump
        // the whole request object into the input. This ensures we can use a
        // stand-in value for 'request' and it will be cast to 'REQUEST'.
        if (strtolower($value) === 'req') {
            $value = 'request';
        }

        return $this->castUpper($value);
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
        return $this->transformDateIntervalToString($this->createDateIntervalFromString($value));
    }

    protected function castTimezone(mixed $value): string
    {
        return (new DateTimeZone($value))->getName();
    }

    /**
     * Compile the event's raw output.
     */
    protected function compile(): self
    {
        $this->output = [];

        $this->beginCalendar();
        $this->createTimezone();
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
            if ($this->has($key)) {
                $this->output[] = $this->getParameter($key);
            }
        }
    }

    /**
     * Close the VCalendar tag.
     */
    protected function endCalendar(): void
    {
        $this->output[] = 'END:VCALENDAR';
    }

    protected function createTimezone(): void
    {
        if (!$this->timezone) {
            return;
        }

        $now = time();
        $year = 31_536_000; // 1 year in seconds
        $timezone = new DateTimeZone($this->timezone);
        $transitions = $timezone->getTransitions($now - $year, $now + $year);

        $hasDaylightSavings = false;
        $standard = $daylight = $transitions[0];

        $this->output[] = 'BEGIN:VTIMEZONE';
        $this->output[] = 'TZID:' . $timezone->getName();
        $this->output[] = 'TZNAME:' . $transitions[0]['abbr'];

        foreach ($transitions as $index => $transition) {
            if ($index === 0) {
                continue;
            }

            if (!$hasDaylightSavings && $transition['isdst']) {
                $daylight = $transition;
                $hasDaylightSavings = true;

                continue;
            }

            if (!$transition['isdst']) {
                $standard = $transition;
            }
        }

        $this->output[] = 'BEGIN:STANDARD';
        $this->output[] = 'DTSTART:' . $this->createDateTime($standard['time']);
        $this->output[] = 'TZOFFSETFROM:' . $this->formatOffset(($daylight)['offset']);
        $this->output[] = 'TZOFFSETTO:' . $this->formatOffset($standard['offset']);
        $this->output[] = 'END:STANDARD';

        if ($hasDaylightSavings) {
            $this->output[] = 'BEGIN:DAYLIGHT';
            $this->output[] = 'DTSTART:' . $this->createDateTime($daylight['time']);
            $this->output[] = 'TZOFFSETFROM:' . $this->formatOffset($standard['offset']);
            $this->output[] = 'TZOFFSETTO:' . $this->formatOffset($daylight['offset']);
            $this->output[] = 'END:DAYLIGHT';
        }

        $this->output[] = 'END:VTIMEZONE';
    }

    protected function formatOffset(int $offset): string
    {
        $hours = floor($offset / 3600);
        $minutes = floor(($offset - $hours * 3600) / 60);

        return sprintf('%+03d%02d', $hours, $minutes);
    }

    /**
     * Create the VEvent and include all its props.
     */
    protected function createEvent(): void
    {
        $this->output[] = 'BEGIN:VEVENT';
        $this->output[] = 'UID:' . $this->getUidAttribute();

        foreach ($this->event_parameters as $key) {

            // if the $key is set and a create method exists then call it.
            if ($this->has($key) &&  method_exists($this, $method = 'create'.ucfirst($key))) {
                $this->{$method}();
                continue;
            }

            $this->output[] = $this->getParameter($key);
        }

        $this->output[] = 'END:VEVENT';
    }

    /**
     * Create the VAlarm with Display action.
     */
    protected function createReminder(): void
    {
        if (!$this->reminder) {
            return;
        }

        $this->output[] = 'BEGIN:VALARM';
        $this->output[] = 'TRIGGER:' . '-' . $this->reminder;
        $this->output[] = 'ACTION:DISPLAY';
        $this->output[] = 'DESCRIPTION:Reminder';
        $this->output[] = 'END:VALARM';
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
            $value = $this->{$key} ?? '';

            return $this->getParameterKey($key) . ':' . $value;
        }
    }

    /**
     * Returns the iCalendar param key.
     */
    protected function getParameterKey(string $name): string
    {
        $key = strtoupper($name);

        return match ($name) {
            'start', 'end' => $this->optionallyAddTimezoneToKey($key),
            'stamp' => 'DT' . $key,
            default => $key,
        };
    }

    /**
     * If there is a timezone transform eligible keys so they include them.
     *
     * @example DTSTART;TZID=Europe/London
     */
    protected function optionallyAddTimezoneToKey(string $key): string
    {
        $prefix = 'DT';
        $suffix = '';

        if ($this->timezone) {
            $suffix = ';TZID=' . (new DateTimeZone($this->timezone))->getName();
        }

        return $prefix . $key . $suffix;
    }

    /**
     * Return the associated value for the supplied iCal param.
     */
    protected function getParameterValue(string $key): string
    {
        if (!$this->has($key)) {
            return '';
        }

        if ($this->hasCaster($key)) {
            return $this->cast($this->{$key}, static::CASTS[$key]);
        }

        return $this->{$key};
    }

    /**
     * Concatenate the invite's parameters.
     */
    protected function getEventParametersAttribute(): array
    {
        return array_filter(
            array_merge(static::VEVENT, static::ALLOWED),
            fn ($key) => match ($key) {
                'uid', 'method', 'timezone' => false,
                default => $this->required($key) || $this->has($key),
            },
        );
    }

    /**
     * Create and return a uid.
     */
    protected function getUidAttribute(): string
    {
        if ($this->uid) {
            return $this->uid;
        }

        $data = random_bytes(16);
        assert(strlen($data) === 16);

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
        $output = array_map(fn ($word) => ucfirst($word), $words);

        return implode($output);
    }

    /**
     * Parses and creates a datetime.
     */
    protected function createDateTime(string|DateTime|Closure $datetime = 'now'): string
    {
        $datetime = new DateTime($datetime);

        if ($this->timezone) {
            $datetime->setTimezone(new DateTimeZone($this->timezone));
        }

        return $datetime->format(static::DATETIME_FORMAT);
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
