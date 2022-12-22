<?php

if (!function_exists('da')) {
    /**
     * dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param ...$args
     *
     * @return void
     */
    function da(...$args)
    {
        if (!in_array(\PHP_SAPI, ['cli', 'phpdbg'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        $varDumper = new Symfony\Component\VarDumper\VarDumper();
        foreach ($args as $x) {
            if ((is_object($x) || is_string($x)) && method_exists($x, 'toArray')) {
                $x = $x->toArray();
            }
            $varDumper->dump($x);
        }
    }
}

if (!function_exists('dda')) {
    /**
     * dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    /**
     * @param ...$args
     *
     * @return void
     */
    function dda(...$args)
    {
        da(...$args);
        exit(1);
    }
}

if (!function_exists('ma')) {
    /**
     * 移动版dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function ma(...$args)
    {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">';
        da(...$args);
    }
}

if (!function_exists('mda')) {
    /**
     * 移动版dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function mda(...$args)
    {
        ma(...$args);
        exit(1);
    }
}

if (!function_exists('number_format')) {
    /**
     * 数字格式化.
     *
     * @param string|int $number  数字
     * @param int        $decimal 保留小数位数
     */
    function number_format($number, $decimal = 2)
    {
        if (null == $number || '' == $number) {
            return '0.00';
        }

        return sprintf('%01.'.$decimal.'f', $number);
    }
}

if (!function_exists('pluck_to_array')) {
    /**
     * [$id$ => $value$, ...] 转成 [['id' => $id$, 'value' => $value$], ...] 方法.
     *
     * @param $array
     * @param string $value
     * @param string $key
     *
     * @return array
     */
    function pluck_to_array($array, $value = 'value', $key = 'id')
    {
        if (method_exists($array, 'toArray')) {
            $array = $array->toArray();
        }
        $data = [];
        foreach ($array as $k => $v) {
            $data[] = [
                $key => $k,
                $value => $v,
            ];
        }

        return $data;
    }
}

if (!function_exists('log_i')) {
    /**
     * 快速日志打印 - 详细.
     * log_info => log_i.
     *
     * @param array|string|null $message 日志信息
     * @param string|null       $name    日志文件名
     * @param string|null       $path    日志写入路径
     * @param int               $max     该目录下最大日志文件数
     */
    function log_i($message = '', $name = 'test', $path = '', $max = 14)
    {
        if (0 == strlen($path)) {
            $path = $name;
        }
        config([
            'logging.channels.'.$path.'_'.$name => [
                'driver' => 'daily',
                'path' => storage_path('logs/'.$path.'/'.$name.'.log'),
                'level' => 'debug',
                'days' => $max,
            ],
        ]);
        $type = '';
        if (function_exists('debug_backtrace') && debug_backtrace()) {
            $first = Illuminate\Support\Arr::first(debug_backtrace());
            if (is_array($first) && isset($first['file']) && isset($first['line'])) {
                $str = substr(str_replace(base_path(), '', $first['file']), 1);
                $type = "{$str}:{$first['line']}";
            }
        }
        if (!is_array($message)) {
            logger()->channel($path.'_'.$name)->info($type.PHP_EOL.$message);
        } else {
            logger()->channel($path.'_'.$name)->info($type);
            logger()->channel($path.'_'.$name)->info($message);
        }
    }
}

if (!function_exists('log_s')) {
    /**
     * 快速日志打印 - 简单.
     * log_sample => log_s.
     *
     * @param string|array|null $message
     * @param string            $path
     * @param string            $name
     * @param bool              $appendTime
     */
    function log_s($message, $path = '', $name = 'log', $appendTime = false)
    {
        if (method_exists($message, 'toArray')) {
            $message = var_export($message->toArray(), true);
        }
        if ($path) {
            $path = trim($path, '/').'/';
            create_dir(storage_path('logs/'.$path));
        }
        $handle = fopen(storage_path('logs/'.$path.$name.'-'.date('Y-m-d').'.log'), 'a');
        if ($appendTime) {
            $message = '['.date('Y-m-d H:i:s').']'.$message;
        }
        fwrite($handle, $message."\n");
        fclose($handle);
    }
}

if (!function_exists('create_dir')) {
    /**
     * 功能：循环检测并创建文件夹.
     *
     * @param string $path 文件夹路径
     */
    function create_dir($path)
    {
        if (!file_exists($path)) {
            create_dir(dirname($path));
            mkdir($path);
        }
    }
}

if (!function_exists('console_line')) {
    /**
     * 命令行模式中, 打印需要的数据.
     *
     * @param $text
     * @param string $type
     */
    function console_line($text, $type = 'line')
    {
        if (app()->runningInConsole()) {
            $types = [
                'info' => 32, 'comment' => 33, 'warn' => 33, 'line' => 37, 'error' => '41;37', 'question' => '46;30',
            ];
            $code = $types[$type] ?? '37';
            // 30黑色，31红色，32绿色，33黄色，34蓝色，35洋红，36青色，37白色，
            echo chr(27).'['.$code.'m'."$text".chr(27).'[0m'.PHP_EOL;
        }
    }
}

if (!function_exists('console_info')) {
    function console_info($text)
    {
        console_line($text, 'info');
    }
}

if (!function_exists('console_comment')) {
    function console_comment($text)
    {
        console_line($text, 'comment');
    }
}

if (!function_exists('console_warn')) {
    function console_warn($text)
    {
        console_line($text, 'warn');
    }
}

if (!function_exists('console_error')) {
    function console_error($text)
    {
        console_line($text, 'error');
    }
}

if (!function_exists('console_question')) {
    function console_question($text)
    {
        console_line($text, 'question');
    }
}
