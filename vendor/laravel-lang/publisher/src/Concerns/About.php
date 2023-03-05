<?php

/**
 * This file is part of the "Laravel-Lang/publisher" project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andrey Helldar <helldar@dragon-code.pro>
 * @copyright 2022 Andrey Helldar
 * @license MIT
 *
 * @see https://github.com/Laravel-Lang/publisher
 */

declare(strict_types=1);

namespace LaravelLang\Publisher\Concerns;

use Composer\InstalledVersions;
use DragonCode\Support\Facades\Helpers\Arr;
use Illuminate\Foundation\Console\AboutCommand;
use LaravelLang\Publisher\Facades\Helpers\Locales;
use LaravelLang\Publisher\Helpers\Config;

trait About
{
    protected function registerAbout(): void
    {
        if (! class_exists(AboutCommand::class)) {
            return;
        }

        $this->pushInformation(fn () => [
            'Installed'         => $this->implodeLocales(Locales::installed()),
            'Protected Locales' => $this->implodeLocales(Locales::protects()),

            'Publisher Version' => $this->getPackageVersion('laravel-lang/publisher'),
        ]);

        $this->pushInformation(fn () => $this->getPackages());
    }

    protected function pushInformation(callable $data): void
    {
        AboutCommand::add('Localization', $data);
    }

    protected function getPackages(): array
    {
        return Arr::of(config(Config::PRIVATE_KEY . '.packages'))
            ->renameKeys(static fn (mixed $key, array $values) => $values['class'])
            ->map(fn (array $values) => $this->getPackageVersion($values['name']))
            ->toArray();
    }

    protected function getPackageVersion(string $package): string
    {
        if (InstalledVersions::isInstalled($package)) {
            return InstalledVersions::getPrettyVersion($package);
        }

        return '<fg=yellow;options=bold>INCORRECT</>';
    }

    protected function implodeLocales(array $locales): string
    {
        return Arr::of($locales)->sort()->implode(', ')->toString();
    }
}
