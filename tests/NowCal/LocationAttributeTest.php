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
}
