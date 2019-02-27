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
     *
     * @param mixed       $value
     * @param string|null $as
     *
     * @return mixed
     */
    protected function cast($value, ?string $as = null)
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
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function castDateTime($value)
    {
        return $this->createDateTime($value);
    }

    /**
     * Cast the specified value as an ISO 8601.2004 interval.
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function castInterval($value)
    {
        return $this->createInterval($value);
    }

    /**
     * Check if the specified key has a caster.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function hasCaster(string $key): bool
    {
        return array_key_exists($key, $this->casts);
    }
}
