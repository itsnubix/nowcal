<?php

namespace NowCal\Traits;

trait HasStaticAccessors
{
    /**
     * Pass the props into the class and create a new instance.
     *
     * @param array $props
     *
     * @return \NowCal\NowCal
     */
    public static function create(array $props = [])
    {
        return new self($props);
    }

    /**
     * Pass the props into the class and build it.
     *
     * @param array $props
     *
     * @deprecated 1.0.0 Prefer "create" syntax
     *
     * @return \NowCal\NowCal
     */
    public static function build(array $props = [])
    {
        return new self($props);
    }

    /**
     * Create an ICS array and output as raw array.
     *
     * @param array $props
     *
     * @return array
     */
    public static function raw(array $props = []): array
    {
        return self::create($props)->raw;
    }

    /**
     * Return the plain text version of the invite.
     *
     * @param array $props
     *
     * @return string
     */
    public static function plain(array $props = []): string
    {
        return self::create($props)->plain;
    }

    /**
     * Return a path to a .ics tempfile.
     *
     * @param array $props
     *
     * @return string
     */
    public static function file(array $props = []): string
    {
        return self::create($props)->file;
    }
}
