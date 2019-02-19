<?php

namespace Tests\NowCal;

use Tests\TestCase;

class LocationAttributeTest extends TestCase
{
    /** @test */
    public function it_can_get_and_set_a_end_time()
    {
        $this->nowcal->location($location = '123 Fake Street NW');

        $this->assertEquals($location, $this->nowcal->location);
    }

    /** @test */
    public function it_includes_location_in_its_output()
    {
        $this->nowcal->location($location = '123 Fake Street NW');

        $this->assertStringContainsString($location, $this->nowcal->plain);
    }

    /** @test */
    public function it_can_take_a_callback_as_a_value()
    {
        $location = 'here';

        $this->nowcal->location(function () use ($location) {
            return $location;
        });

        $this->assertEquals($location, $this->nowcal->location);
    }
}
