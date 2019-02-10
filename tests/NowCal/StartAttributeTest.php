<?php

namespace Tests\NowCal;

use Carbon\Carbon;
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

        $this->assertStringContainsString(Carbon::parse($time)->format($format), $this->nowcal->plain);
    }
}
