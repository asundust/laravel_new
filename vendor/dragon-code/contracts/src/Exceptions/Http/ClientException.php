<?php

/*
 * This file is part of the "dragon-code/contracts" project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Andrey Helldar <helldar@ai-rus.com>
 *
 * @copyright 2021 Andrey Helldar
 *
 * @license MIT
 *
 * @see https://github.com/TheDragonCode/contracts
 */

declare(strict_types=1);

namespace DragonCode\Contracts\Exceptions\Http;

use DragonCode\Contracts\Http\Builder;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

interface ClientException extends HttpExceptionInterface
{
    public function __construct(Builder $uri, ?string $reason = null);
}
