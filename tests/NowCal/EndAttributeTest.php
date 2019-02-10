<?php

namespace Tests\NowCal;

use Carbon\Carbon;
use Tests\TestCase;

class EndAttributeTest extends TestCase
{
    /** @test */
    public function it_can_get_and_set_a_end_time()
    {
        $this->nowcal->end($time = 'now');

        $this->assertEquals($time, $this->nowcal->end);
    }

    /** @test */
    public function it_casts_end_as_a_datetime()
    {
        $this->nowcal->end($time = 'October 5, 2019 6:03PM');
        $format = 'Ymd\THis\Z';

        $this->assertStringContainsString(Carbon::parse($time)->format($format), $this->nowcal->plain);
    }
}
