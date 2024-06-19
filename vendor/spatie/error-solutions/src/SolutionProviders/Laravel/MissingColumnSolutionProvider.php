<?php

namespace Spatie\ErrorSolutions\SolutionProviders\Laravel;

use Illuminate\Database\QueryException;
use Spatie\ErrorSolutions\Contracts\HasSolutionsForThrowable;
use Spatie\ErrorSolutions\Solutions\Laravel\RunMigrationsSolution;
use Throwable;

class MissingColumnSolutionProvider implements HasSolutionsForThrowable
{
    /**
     * See https://dev.mysql.com/doc/refman/8.0/en/server-error-reference.html#error_er_bad_field_error.
     */
    const MYSQL_BAD_FIELD_CODE = '42S22';

    public function canSolve(Throwable $throwable): bool
    {
        if (! $throwable instanceof QueryException) {
            return false;
        }

        return  $this->isBadTableErrorCode($throwable->getCode());
    }

    protected function isBadTableErrorCode(string $code): bool
    {
        return $code === static::MYSQL_BAD_FIELD_CODE;
    }

    public function getSolutions(Throwable $throwable): array
    {
        return [new RunMigrationsSolution('A column was not found')];
    }
}
