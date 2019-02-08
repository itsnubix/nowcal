<?php

namespace NowCal\Traits;

use Ramsey\Uuid\Uuid;

trait HasMutators
{
    /**
     * Concatenate the invite's parameters.
     *
     * @return array
     */
    public function getEventParametersAttribute(): array
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
        return $this->output;
    }

    /**
     * Return the invite's data as plain text.
     *
     * @return string
     */
    public function getPlainAttribute(): string
    {
        return implode(PHP_EOL, $this->output);
    }

    /**
     * Creates a tempfile and returns its path.
     *
     * @return string
     */
    public function getFileAttribute(): string
    {
        return 'temp file';
    }
}
