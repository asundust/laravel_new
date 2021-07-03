<?php

namespace Mnabialek\LaravelVersion\Tests;

use Illuminate\Container\Container;
use Mockery;
use Mnabialek\LaravelVersion\Version;

class VersionTest extends UnitTestCase
{
    /**
     * @var Container
     */
    protected $app;

    /**
     * @var Version
     */
    protected $version;

    protected function setUp()
    {
        $this->app = Mockery::mock(Container::class);
        $this->version = new Version($this->app);
    }

    /** @test */
    public function it_confirms_when_application_is_laravel()
    {
        $this->app->shouldReceive('version')->withNoArgs()->once()->andReturn('5.5.28');
        $this->assertTrue($this->version->isLaravel());

        $this->app->shouldReceive('version')->withNoArgs()->once()->andReturn('Lumen (5.5.2) (Laravel Components 5.5.*)');
        $this->assertFalse($this->version->isLaravel());
    }

    /** @test */
    public function it_confirms_when_application_is_lumen()
    {
        $this->app->shouldReceive('version')->withNoArgs()->once()->andReturn('Lumen (5.5.2) (Laravel Components 5.5.*)');
        $this->assertTrue($this->version->isLumen());

        $this->app->shouldReceive('version')->withNoArgs()->once()->andReturn('5.5.28');
        $this->assertFalse($this->version->isLumen());
    }

    /** @test */
    public function it_returns_full_version_string()
    {
        $version = 'Lumen 23238 test version';
        $this->app->shouldReceive('version')->withNoArgs()->once()->andReturn($version);
        $this->assertSame($version, $this->version->full());
    }

    /** @test */
    public function it_returns_valid_application_version_for_laravel()
    {
        $version = '5.5.28';
        $this->app->shouldReceive('version')->withNoArgs()->twice()->andReturn($version);
        $this->assertSame($version, $this->version->get());
    }

    /** @test */
    public function it_returns_valid_application_version_for_lumen()
    {
        $version = 'Lumen (5.5.2) (Laravel Components 5.5.*)';
        $this->app->shouldReceive('version')->withNoArgs()->twice()->andReturn($version);
        $this->assertSame('5.5.2', $this->version->get());
    }

    /** @test */
    public function it_confirms_when_application_is_minimum_at_given_version_for_laravel()
    {
        $version = '5.5.28';
        $this->app->shouldReceive('version')->withNoArgs()->times(6)->andReturn($version);
        $this->assertTrue($this->version->min('5.5.27'));
        $this->assertTrue($this->version->min('5.5.28'));
        $this->assertFalse($this->version->min('5.5.29'));
    }

    /** @test */
    public function it_confirms_when_application_is_minimum_at_given_version_for_lumen()
    {
        $version = 'Lumen (5.5.28) (Laravel Components 5.5.*)';
        $this->app->shouldReceive('version')->withNoArgs()->times(6)->andReturn($version);
        $this->assertTrue($this->version->min('5.5.27'));
        $this->assertTrue($this->version->min('5.5.28'));
        $this->assertFalse($this->version->min('5.5.29'));
    }
}
