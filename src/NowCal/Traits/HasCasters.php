<?php

namespace NowCal\Traits;

trait HasCasters
{
    /**
     * All the properties that need to be cast.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'interval',
        'created' => 'datetime',
        'stamp' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

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
        return array_key_exists($key, $this->casts);
    }
}
