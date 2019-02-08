<?php

namespace App\Support\Calendar\Traits;

trait HasHelpers
{
    /**
     * Get the class' property.
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function get(string $key)
    {
        if ($this->allowed($key)) {
            return $this->{$key};
        }

        return null;
    }

    /**
     * Set the class' properties.
     *
     * @param string|array $key
     * @param mixed        $val
     */
    protected function set($key, $val = null)
    {
        if (is_array($key)) {
            $this->merge($key);
        } else {
            if ($this->allowed($key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * Check if the class has a key.
     *
     * @param string $key
     *
     * @return bool
     */
    protected function has(string $key): bool
    {
        return !is_null($this->{$key});
    }

    /**
     * Merge multiple properties.
     *
     * @param array $props
     */
    protected function merge(array $props)
    {
        foreach ($props as $key => $val) {
            $this->set($key, $val);
        }
    }
}
