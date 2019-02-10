<?php

namespace Tests\NowCal;

use Tests\TestCase;

class SummaryAttributeTest extends TestCase
{
    /** @test */
    public function it_can_get_and_set_a_summary()
    {
        $this->nowcal->summary($summary = 'lorem ipsum dolor sit');

        $this->assertEquals($summary, $this->nowcal->summary);
    }

    /** @test */
    public function it_includes_the_summary_in_its_output()
    {
        $this->nowcal->summary($summary = 'This is my summary');

        $this->assertStringContainsString($summary, $this->nowcal->plain);
    }
}
