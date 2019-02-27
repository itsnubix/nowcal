<?php

namespace Tests\NowCal;

use Tests\TestCase;
use Carbon\CarbonInterval;

class DurationAttributeTest extends TestCase
{
    /** @test */
    public function it_can_get_set_a_duration()
    {
        $this->nowcal->duration($time = '1h');

        $this->assertEquals($time, $this->nowcal->duration);
    }

    /** @test */
    public function it_casts_end_as_an_iso_duration()
    {
        $this->nowcal->duration($duration = '1h');
        var_dump($this->nowcal->plain);

        $this->assertStringContainsString(CarbonInterval::fromString($duration)->spec(), $this->nowcal->plain);
    }

    /** @test */
    public function it_can_take_a_callback_as_a_value()
    {
        $duration = '5m';

        $this->nowcal->end(function () use ($duration) {
            return $duration;
        });

        $this->assertEquals($duration, $this->nowcal->end);
    }

    /** @test */
    public function it_cannot_have_both_an_end_and_duration()
    {
    }
}
