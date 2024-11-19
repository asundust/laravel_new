<?php

if (!function_exists('da')) {
    /**
     * dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param ...$vars
     *
     * @return void
     */
    function da(...$vars): void
    {
        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg', 'embed'], true) && !headers_sent()) {
            header('HTTP/1.1 500 Internal Server Error');
        }
        $varDumper = new \Symfony\Component\VarDumper\VarDumper();
        foreach ($vars as $x) {
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
     * @param mixed $vars
     */
    /**
     * @param ...$vars
     *
     * @return void
     */
    function dda(...$vars): void
    {
        da(...$vars);
        exit(1);
    }
}

if (!function_exists('ma')) {
    /**
     * 移动版dd打印封装 不断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $vars
     */
    function ma(...$vars): void
    {
        echo '<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no">';
        da(...$vars);
    }
}

if (!function_exists('mda')) {
    /**
     * 移动版dd打印封装 并断点
     * 如果能转成toArray()则转成数组.
     *
     * @param mixed $vars
     */
    function mda(...$vars): void
    {
        ma(...$vars);
        exit(1);
    }
}

if (!function_exists('num_format')) {
    /**
     * 数值格式化.
     *
     * @param mixed $num  数值
     * @param int $decimal 保留小数位数
     *
     * @return string
     */
    function num_format(mixed $num, int $decimal = 2): string
    {
        if ($num === null || $num === '') {
            $num = 0;
        }
        return number_format((float)$num, $decimal, '.', '');
    }
}

if (!function_exists('pluck_to_array')) {
    /**
     * [$VALUE$ => $LABEL$, ...] 转成 [['value' => $VALUE$, 'label' => $LABEL$], ...] 方法.
     *
     * @param $array
     * @param string $label 值（展示）
     * @param string $value 键（隐藏）
     * @return array
     */
    function pluck_to_array($array, string $label = 'label', string $value = 'value'): array
    {
        if ((is_object($array) || is_string($array)) && method_exists($array, 'toArray')) {
            $array = $array->toArray();
        }
        $data = [];
        foreach ($array as $k => $v) {
            $data[] = [
                $value => $k,
                $label => $v,
            ];
        }

        return $data;
    }
}

if (!function_exists('string_to_array')) {
    /**
     * 拼接字符串转成数组
     *
     * @param mixed $string 原始字符串
     * @param array $replaces 额外被替换的字符
     * @param string $separator 切割字符，默认为“,”
     * @return array
     */
    function string_to_array(mixed $string, array $replaces = [], string $separator = ','): array
    {
        foreach ($replaces as $replace) {
            $string = str_replace($replace, $separator, $string);
        }
        $arr = array_filter(array_unique(explode($separator, $string)));
        foreach ($arr as &$value) {
            $value = trim($value);
        }
        return $arr;
    }
}

if (!function_exists('alog')) {
    /**
     * 自定义快捷日志函数
     *
     * @param string $path 日志路径，可以多级
     * @param string $name 日志文件名
     * @param int $days 保留天数
     * @param string $driver 驱动
     * @param array $configs 额外配置
     * @param string $channel 通道，用于日志的基础配置
     * @return \Asundust\Helpers\Support\Alog
     */
    function alog(string $name = 'custom', string $path = 'custom', int $days = 14, string $driver = 'daily', array $configs = [], string $channel = 'daily'): \Asundust\Helpers\Support\Alog
    {
        return new \Asundust\Helpers\Support\Alog($name, $path, $days, $driver, $configs, $channel);
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
        if ((is_object($message) || is_string($message)) && method_exists($message, 'toArray')) {
            $message = var_export($message->toArray(), true);
        } elseif (is_array($message)) {
            $message = var_export($message, true);
        }
        if ($path) {
            $path = trim($path, '/') . '/';
            create_dir(storage_path('logs/' . $path));
        }
        $handle = fopen(storage_path('logs/' . $path . $name . '-' . date('Y-m-d') . '.log'), 'a');
        if ($appendTime) {
            $message = '[' . date('Y-m-d H:i:s') . ']' . $message;
        }
        fwrite($handle, $message . "\n");
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

if (!function_exists('get_package_version')) {
    /**
     * 获取已安装扩展的版本号.
     *
     * @param $packageName
     *
     * @return false|string
     */
    function get_package_version($packageName): bool|string
    {
        try {
            return \Composer\InstalledVersions::getVersion($packageName);
        } catch (\OutOfBoundsException $exception) {
            return false;
        }
    }
}

if (!function_exists('new_request')) {
    /**
     * 新建一个请求对象.
     *
     * @param array $params
     * @return \GuzzleHttp\Client
     */
    function new_request(array $params = []): \GuzzleHttp\Client
    {
        $config = array_merge([
            'timeout' => 10,
            'verify' => false,
            'http_errors' => false,
        ], $params);
        return new \GuzzleHttp\Client($config);
    }
}

if (!function_exists('api_success')) {
    /**
     * Api成功返回
     *
     * @param mixed $message
     * @param array $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    function api_success(mixed $message, array $data = [], int $code = 0): \Illuminate\Http\JsonResponse
    {
        if (!is_string($message)) {
            $data = $message;
            $message = '';
        }
        return response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ]);
    }
}

if (!function_exists('api_error')) {
    /**
     * Api错误返回
     *
     * @param mixed $message
     * @param array $data
     * @param int $code
     * @return void
     */
    function api_error(mixed $message, array $data = [], int $code = 1): void
    {
        if (!is_string($message)) {
            $data = $message;
            $message = '操作失败';
        }
        response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ])->throwResponse();
    }
}
