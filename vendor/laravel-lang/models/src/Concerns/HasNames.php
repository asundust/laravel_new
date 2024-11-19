<?php

declare(strict_types=1);

namespace LaravelLang\Models\Concerns;

use LaravelLang\Config\Facades\Config;

trait HasNames
{
    public function translationModelName(): string
    {
        return static::class . Config::shared()->models->suffix;
    }
}
