<?php

if (!function_exists('log_channel')) {
    /**
     * 返回指定通道的日志实例.
     *
     * @param $channel
     *
     * @return mixed
     */
    function log_channel($channel)
    {
        return logger()->channel($channel);
    }
}

if (!function_exists('pl')) {
    /**
     * 快速日志打印.
     *
     * @param string $message 日志信息
     * @param string $name    日志文件名
     * @param string $path    日志写入路径
     * @param int    $max     该目录下最大日志文件数
     */
    function pl($message = '', $name = 'test', $path = '', $max = 14)
    {
        if (0 == strlen($path)) {
            $path = $name;
        }
        config([
            'logging.channels.' . $path . '_' . $name => [
                'driver' => 'daily',
                'path' => storage_path('logs/' . $path . '/' . $name . '.log'),
                'level' => 'debug',
                'days' => $max,
            ],
        ]);
        $type = '';
        if (function_exists('debug_backtrace') && debug_backtrace()) {
            $first = Arr::first(debug_backtrace());
            if (is_array($first) && isset($first['file']) && isset($first['line'])) {
                $str = substr(str_replace(base_path(), '', $first['file']), 1);
                $type = "{$str}:{$first['line']}";
            }
        }
        if (!is_array($message)) {
            logger()->channel($path . '_' . $name)->info($type . PHP_EOL . $message);
        } else {
            logger()->channel($path . '_' . $name)->info($type);
            logger()->channel($path . '_' . $name)->info($message);
        }
    }
}

if (!function_exists('api_res')) {
    /**
     * 封装返回数据.
     *
     * @param string $msg
     * @param array  $data
     * @param int    $code
     *
     * @return array
     */
    function api_res($msg, $data = [], $code = 0)
    {
        return compact('msg', 'data', 'code');
    }
}

if (!function_exists('api_ok')) {
    /**
     * 封装返回数据-成功
     *
     * @param string|array $msg
     * @param array|int    $data
     * @param int          $code
     *
     * @return array
     */
    function api_ok($msg, $data = [], $code = 0)
    {
        if (is_string($msg)) {
            return api_res($msg, $data, $code);
        }

        return api_res('', $msg, is_int($data) ? $data : $code);
    }
}

if (!function_exists('da')) {
    /**
     * dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function da(...$args)
    {
        $varDumper = new Symfony\Component\VarDumper\VarDumper();
        foreach ($args as $x) {
            if (method_exists($x, 'toArray')) {
                $x = $x->toArray();
            }
            $varDumper->dump($x);
        }
    }
}

if (!function_exists('dad')) {
    /**
     * dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function dad(...$args)
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

if (!function_exists('mad')) {
    /**
     * 移动版dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $args
     */
    function mad(...$args)
    {
        ma(...$args);
        exit(1);
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
            echo chr(27) . '[' . $code . 'm' . "$text" . chr(27) . '[0m' . PHP_EOL;
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

if (!function_exists('sc_send')) {
    /**
     * Server酱推送
     *
     * @param $text
     * @param string $desc
     * @param string $key
     *
     * @return bool|false|string
     */
    function sc_send($text, $desc = '', $key = '')
    {
        if (!$key) {
            $key = config('sc_send_key');
        }
        if (!$key) {
            return false;
        }
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query([
                    'text' => $text,
                    'desp' => $desc,
                ]),
            ],
        ]);

        return $result = file_get_contents('https://sc.ftqq.com/' . $key . '.send', false, $context);
    }
}

if (!function_exists('admin_switch_arr')) {
    /**
     * admin系统的switch选项.
     *
     * @param $arr
     * @param bool $isOpposite
     *
     * @return array
     */
    function admin_switch_arr($arr, $isOpposite = true)
    {
        $keys = array_keys($arr);
        $key1 = $isOpposite ? 1 : 0;
        $key2 = $isOpposite ? 0 : 1;

        return [
            'on' => ['value' => $keys[$key1], 'text' => $arr[$keys[$key1]], 'color' => 'success'],
            'off' => ['value' => $keys[$key2], 'text' => $arr[$keys[$key2]], 'color' => 'danger'],
        ];
    }
}
