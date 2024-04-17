<?php

namespace NowCal\Traits;

trait HasStaticAccessors
{
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
}
