<?php

namespace NowCal\Traits;

trait HasStaticAccessors
{
    public static function build(array $props)
    {
        return (new self($props))->compile();
    }

    /**
     * Create an ICS array and output as raw array.
     *
     * @param array $props
     *
     * @return array
     */
    public static function raw(array $props)
    {
        return self::build($props)->raw;
    }

    /**
     * Return the plain text version of the invite.
     *
     * @param array $props
     *
     * @return string
     */
    public static function plain(array $props)
    {
        return self::build($props)->plain;
    }

    public static function file(array $props)
    {
        return self::build($props)->file;
    }
}
