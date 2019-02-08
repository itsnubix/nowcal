<?php

namespace App\Support\Calendar\Traits;

trait HasCasters
{
    /**
     * Undocumented variable.
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

    protected function cast($value, $as)
    {
        switch ($as) {
            case 'datetime':
                return $this->castDateTime($value);
            default:
                return $value;
        }
    }

    protected function castDateTime($value): string
    {
        return $this->createDateTime($value);
    }

    protected function hasCaster($key): bool
    {
        return array_key_exists($key, $this->casts);
    }
}
