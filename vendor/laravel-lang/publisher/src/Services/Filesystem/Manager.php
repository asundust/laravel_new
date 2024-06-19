<?php

/**
 * This file is part of the "laravel-lang/publisher" project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andrey Helldar <helldar@dragon-code.pro>
 * @copyright 2024 Laravel Lang Team
 * @license MIT
 *
 * @see https://laravel-lang.com
 */

declare(strict_types=1);

namespace LaravelLang\Publisher\Services\Filesystem;

use DragonCode\Contracts\Support\Filesystem;
use DragonCode\Support\Concerns\Resolvable;
use LaravelLang\Publisher\Concerns\Has;

class Manager implements Filesystem
{
    use Has;
    use Resolvable;

    public function load(string $path): array
    {
        return $this->filesystem($path)->load($path);
    }

    public function store(string $path, $content): string
    {
        return $this->filesystem($path)->store($path, $content);
    }

    protected function filesystem(string $path): Filesystem
    {
        return $this->hasJson($path)
            ? static::resolveInstance(Json::class)
            : static::resolveInstance(Php::class);
    }
}
