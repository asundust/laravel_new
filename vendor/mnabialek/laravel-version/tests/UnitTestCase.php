<?php

namespace Mnabialek\LaravelVersion\Tests;

use Mockery;
use PHPUnit\Framework\TestCase;

class UnitTestCase extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }
}
