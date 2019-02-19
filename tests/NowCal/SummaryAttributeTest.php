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

    /** @test */
    public function it_can_take_a_callback_as_a_value()
    {
        $summary = 'my event';

        $this->nowcal->summary(function () use ($summary) {
            return $summary;
        });

        $this->assertEquals($summary, $this->nowcal->summary);
    }
}
