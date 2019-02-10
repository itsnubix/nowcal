<?php

namespace Tests;

use NowCal\NowCal;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $nowcal;

    public function setUp(): void
    {
        parent::setUp();

        $this->nowcal = new NowCal([]);
    }
}
