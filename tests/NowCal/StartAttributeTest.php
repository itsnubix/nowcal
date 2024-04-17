<?php

namespace Tests\NowCal;

use DateTime;
use Tests\TestCase;

class StartAttributeTest extends TestCase
{
    /** @test */
    public function it_can_get_and_set_a_start_time()
    {
        $this->nowcal->start($time = 'now');

        $this->assertEquals($time, $this->nowcal->start);
    }

    /** @test */
    public function it_casts_start_as_a_datetime()
    {
        $this->nowcal->start($time = 'October 5, 2019 6:03PM');
        $format = 'Ymd\THis\Z';

        $this->assertStringContainsString((new DateTime($time))->format($format), $this->nowcal->plain);
    }

    /** @test */
    public function it_can_take_a_callback_as_a_value()
    {
        $time = 'now';

        $this->nowcal->start(function () use ($time) {
            return $time;
        });

        $this->assertEquals($time, $this->nowcal->start);
    }
}
