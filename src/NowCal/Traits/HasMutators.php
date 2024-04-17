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
     */
    public function __get(string $key): mixed
    {
        if (method_exists(self::class, $method = 'get' . $this->convertStringToPascalCase($key) . 'Attribute')) {
            return $this->{$method}();
        }
    }

    /**
     * Concatenate the invite's parameters.
     */
    protected function getEventParametersAttribute(): array
    {
        return array_filter(
            array_merge($this->event, $this->allowed),
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
        return Uuid::uuid4()->toString();
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

        return implode(self::$crlf, $this->output);
    }

    /**
     * Creates a tempfile and returns its path.
     */
    public function getFileAttribute(): string
    {
        $filename = tempnam(sys_get_temp_dir(), 'event_') . '.ics';
        file_put_contents($filename, $this->plain . self::$crlf);

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
}
