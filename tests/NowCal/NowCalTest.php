<?php

namespace Tests\NowCal;

use Tests\TestCase;

class NowCalTest extends TestCase
{
    /** @test */
    public function it_can_get_a_raw_array_output()
    {
        $raw = $this->nowcal->raw;

        $this->assertIsArray($raw);
    }

    /** @test */
    public function it_can_get_a_plaintext_output()
    {
        $plain = $this->nowcal->plain;

        $this->assertIsString($plain);
    }

    /** @test */
    public function it_can_export_a_path_to_a_file()
    {
        $file = $this->nowcal->file;

        $this->assertIsString($file);
    }
}
