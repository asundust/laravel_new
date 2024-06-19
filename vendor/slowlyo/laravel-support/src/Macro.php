<?php

namespace Slowlyo\Support;

use Illuminate\Database\Eloquent\Builder;

class Macro
{
    public static function handle()
    {
        // findInSet
        Builder::macro('findInSet', function ($column, $value) {
            return $this->whereRaw("FIND_IN_SET(?, $column)", [$value]);
        });

        // toRawSql
        \Illuminate\Database\Query\Builder::macro('toRawSql', function () {
            return array_reduce($this->getBindings(), function ($sql, $binding) {
                return preg_replace('/\?/', is_numeric($binding) ? $binding : "'" . $binding . "'", $sql, 1);
            }, $this->toSql());
        });

        Builder::macro('toRawSql', function () {
            return ($this->getQuery()->toRawSql());
        });
    }
}
