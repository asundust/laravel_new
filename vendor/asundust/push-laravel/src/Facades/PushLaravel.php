<?php

namespace Asundust\PushLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class PushLaravel.
 *
 * @method static \Asundust\PushLaravel\PushLaravel send(string $title, ?string $content = null, ?string $url = null, ?string $urlTitle = null)
 */
class PushLaravel extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'push-laravel';
    }

    /**
     * @param string $name
     *
     * @return \Asundust\PushLaravel\PushLaravel
     */
    public static function config($name = '')
    {
        return $name ? app('push-laravel.'.$name) : app('push-laravel');
    }
}
