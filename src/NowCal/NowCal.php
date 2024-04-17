<?php

namespace NowCal;

class NowCal
{
    use Traits\HasCasters;
    use Traits\HasMutators;
    use Traits\HasDateTimes;
    use Traits\HasAttributes;
    use Traits\HasStaticAccessors;

    /**
     * iCalendar Product Identifier.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.3
     */
    private string $prodid = '-//itsnubix//NowCal//EN';

    /**
     * Specifies the minimum iCalendar specification that is required
     * in order to interpret the iCalendar object.
     *
     * @see https://tools.ietf.org/html/rfc5545#section-3.7.4
     */
    protected string $version = '2.0';

    /**
     * The .ics raw array output.
     */
    protected array $output = [];

    /**
     * Instantiate the NowCal class.
     */
    public function __construct(array $params = [])
    {
        $this->merge($params);
    }

    /**
     * Compile the event's raw output.
     */
    protected function compile(): self
    {
        $this->output = [];

        $this->beginCalendar();
        $this->createEvent();
        $this->endCalendar();

        return $this;
    }

    /**
     * Open the VCalendar tag and add necessary props.
     */
    protected function beginCalendar(): void
    {
        $this->output[] = 'BEGIN:VCALENDAR';

        foreach ($this->calendar as $key) {
            $this->output[] = $this->getParameter($key);
        }
    }

    /**
     * Close the VCalendar tag.
     */
    protected function endCalendar(): void
    {
        $this->output[] = 'END:VCALENDAR';
    }

    /**
     * Create the VEvent and include all its props.
     */
    protected function createEvent(): void
    {
        $this->output[] = 'BEGIN:VEVENT';

        foreach ($this->event_parameters as $key) {
            $this->output[] = $this->getParameter($key);
        }

        $this->output[] = 'END:VEVENT';
    }

    /**
     * Get the provided parameter from the ICS spec. If not
     * included in the spec then fail. If not provided but
     * required then throw exception.
     *
     * @throws Exception
     */
    protected function getParameter(string $key): string
    {
        if ($this->has($key)) {
            return $this->getParameterKey($key) . ':' . $this->getParameterValue($key);
        }

        if ($this->required($key)) {
            throw new \Exception('Key "' . $key . '" is not set but is required');
        }
    }

    /**
     * Returns the iCalendar param key.
     */
    protected function getParameterKey(string $name): string
    {
        $key = strtoupper($name);

        switch ($name) {
            case 'start':
            case 'end':
            case 'stamp':
                return 'DT' . $key;
            default:
                return $key;
        }
    }

    /**
     * Return the associated value for the supplied iCal param.
     */
    protected function getParameterValue(string $key): string
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
