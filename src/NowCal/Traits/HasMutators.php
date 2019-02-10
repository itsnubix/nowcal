<?php

namespace NowCal\Traits;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str;

trait HasMutators
{
    /**
     * CRLF return.
     *
     * @var string
     */
    protected static $crlf = "\r\n";

    /**
     * Magic method for getting computed properties.
     *
     * @param string $name
     */
    public function __get(string $key)
    {
        if (method_exists(self::class, $method = 'get' . Str::studly($key) . 'Attribute')) {
            return $this->{$method}();
        }
    }

    /**
     * Concatenate the invite's parameters.
     *
     * @return array
     */
    protected function getEventParametersAttribute(): array
    {
        return array_filter(
            array_merge($this->event, $this->allowed),
            function ($key) {
                return $this->has($key);
            }
        );
    }

    /**
     * Create and return a UUID.
     *
     * @return string
     */
    protected function getUidAttribute(): string
    {
        return Uuid::uuid4()->toString();
    }

    /**
     * Create and return a timestamp.
     *
     * @return string
     */
    protected function getStampAttribute(): string
    {
        return $this->createDateTime();
    }

    /**
     * Create and return the created at timestamp.
     *
     * @return string
     */
    protected function getCreatedAttribute(): string
    {
        return $this->createDateTime();
    }

    /**
     * Return the invite's raw output array.
     *
     * @return array
     */
    public function getRawAttribute(): array
    {
        $this->compile();

        return $this->output;
    }

    /**
     * Return the invite's data as plain text.
     *
     * @return string
     */
    public function getPlainAttribute(): string
    {
        $this->compile();

        return implode(self::$crlf, $this->output);
    }

    /**
     * Creates a tempfile and returns its path.
     *
     * @return string
     */
    public function getFileAttribute(): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'event_') . '.ics';
        file_put_contents($filename, $this->plain . self::$crlf);

        return $filename;
    }

    protected function getFileNameAttribute()
    {
        return $this->uid . '_event.ics';
    }
}
