<?php

namespace Mnabialek\LaravelVersion;

use Illuminate\Container\Container;

class Version
{
    /**
     * @var Container
     */
    private $app;

    /**
     * Version constructor.
     *
     * @param Container $app
     */
    public function __construct(Container $app)
    {
        $this->app = $app;
    }

    /**
     * Verify whether application is Laravel.
     *
     * @return bool
     */
    public function isLaravel()
    {
        return mb_strpos($this->full(), 'Lumen') === false;
    }

    /**
     * Verify whether application is Lumen.
     *
     * @return bool
     */
    public function isLumen()
    {
        return ! $this->isLaravel();
    }

    /**
     * Get original application version string.
     * 
     * @return string
     */
    public function full()
    {
        return $this->app->version();
    }

    /**
     * Get application version.
     *
     * @return string
     */
    public function get()
    {
        $version = $this->full();

        if ($this->isLumen()) {
            $version = $this->lumenVersion($version);
        }

        return $version;
    }

    /**
     * Verify whether application version is minimum at given version.
     *
     * @param string $version
     *
     * @return bool
     */
    public function min($version)
    {
        return version_compare($this->get(), $version) >= 0;
    }

    /**
     * Get Lumen application version.
     *
     * @param string $version
     *
     * @return string
     */
    protected function lumenVersion($version)
    {
        if (preg_match('#Lumen\s*\((.*)\)#U', $version, $matches)) {
            $version = $matches[1];
        }

        return $version;
    }
}
