<?php

namespace App\Console\Commands;

use Tymon\JWTAuth\Console\JWTGenerateSecretCommand as BaseJWTGenerateSecretCommand;

class JWTGenerateSecretForceCommand extends BaseJWTGenerateSecretCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'jwt:secret-force
        {--s|show : Display the key instead of modifying files.}
        {--always-no : Skip generating key if it already exists.}
        {--f|force : Skip confirmation when overwriting an existing key.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '强制设置JWT密钥';

    /**
     * Check if the modification is confirmed.
     *
     * @return bool
     */
    protected function isConfirmed()
    {
        return true;
    }
}
