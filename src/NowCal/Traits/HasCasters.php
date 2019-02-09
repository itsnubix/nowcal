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
        'created' => 'datetime',
        'stamp' => 'datetime',
        'start' => 'datetime',
        'end' => 'datetime',
    ];

    /**
     * The format to use for casting of datetimes.
     *
     * @var string
     */
    protected $datetime_format = 'Ymd\THis\Z';

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
    protected function castDateTime($value): string
    {
        return $this->createDateTime($value);
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
