<?php

namespace Slowlyo\Support;

use Illuminate\Support\Facades\DB;

class SqlRecord
{
    public static array $sql = [];

    public static function listen()
    {
        DB::listen(function ($query) {
            $bindings = $query->bindings;
            $sql      = $query->sql;

            foreach ($bindings as $replace) {
                $value = is_numeric($replace) ? $replace : "'" . $replace . "'";
                $sql   = preg_replace('/\?/', $value, $sql, 1);
            }

            $sql = sprintf('[%s ms] %s', $query->time, $sql);

            self::$sql[] = $sql;
        });
    }
}
