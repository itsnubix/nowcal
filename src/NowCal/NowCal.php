<?php

namespace App\Support\Calendar;

use Illuminate\Support\Str;

class NowCal
{
    use Traits\HasAttributes,
        Traits\HasCasters,
        Traits\HasDateTimes,
        Traits\HasHelpers,
        Traits\HasMutators,
        Traits\HasStaticAccessors;

    /**
     * iCalendar Product Identifier.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.3
     *
     * @var string
     */
    private $prodid = '-//itsnubix//NowCal//EN';

    /**
     * Specifies the minimum iCalendar specification that is required
     * in order to interpret the iCalendar object.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.4
     *
     * @var string
     */
    protected $version = '2.0';

    /**
     * The .ics raw array output.
     *
     * @var array
     */
    protected $output = [];

    /**
     * Instantiate the NowCal class.
     *
     * @param array $params
     */
    public function __construct(array $params)
    {
        $this->merge($params);
    }

    /**
     * Magic method for getting computed properties.
     *
     * @param string $name
     */
    public function __get(string $key)
    {
        if (method_exists(self::class, $method = 'get'.Str::studly($key).'Attribute')) {
            return $this->{$method}();
        }
    }

    /**
     * Compile the event's raw output.
     *
     * @return NowCal
     */
    protected function compile()
    {
        $this->output = [];

        $this->beginCalendar();
        $this->createEvent();
        $this->endCalendar();

        return $this;
    }

    /**
     * Open the VCalendar tag and necessary props.
     */
    protected function beginCalendar()
    {
        $this->output[] = 'BEGIN:VCALENDAR';

        foreach ($this->calendar as $key) {
            $this->output[] = $this->getParameter($key);
        }
    }

    protected function endCalendar()
    {
        $this->output[] = 'END:VCALENDAR';
    }

    protected function createEvent()
    {
        $this->output[] = 'BEGIN:VEVENT';

        foreach ($this->event_parameters as $key) {
            $this->output[] = $this->getParameter($key);
        }

        $this->output[] = 'END:VEVENT';
    }

    protected function endEvent()
    {
        $this->output[] = 'END:VEVENT';
    }

    protected function getParameter($key)
    {
        if ($this->has($key)) {
            return $this->getParameterKey($key).':'.$this->getParameterValue($key);
        }

        if ($this->required($key)) {
            throw new \Exception('Key "'.$key.'" is not set but is required');
        }
    }

    protected function getParameterKey($name)
    {
        $key = Str::upper($name);

        switch ($key) {
            case 'start':
            case 'end':
            case 'stamp':
                return 'DT'.$key;
            default:
                return $key;
        }
    }

    protected function getParameterValue($key)
    {
        if ($this->has($key)) {
            if ($this->hasCaster($key)) {
                return $this->cast($this->{$key}, $this->casts[$key]);
            }

            return $this->{$key};
        }

        return null;
    }
}
