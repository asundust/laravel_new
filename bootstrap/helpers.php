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
            'logging.channels.'.$path.'_'.$name => [
                'driver' => 'daily',
                'path' => storage_path('logs/'.$path.'/'.$name.'.log'),
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
            logger()->channel($path.'_'.$name)->info($type.PHP_EOL.$message);
        } else {
            logger()->channel($path.'_'.$name)->info($type);
            logger()->channel($path.'_'.$name)->info($message);
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

if (!function_exists('sc_send')) {
    /**
     * Server酱推送
     *
     * @param        $text
     * @param string $desc
     * @param string $key
     *
     * @return false|\Psr\Http\Message\ResponseInterface
     */
    function sc_send($text, $desc = '', $key = '')
    {
        if (!$key) {
            $key = config('sc_send_key');
        }
        if (!$key) {
            return false;
        }

        $response = (new \GuzzleHttp\Client([
            'timeout' => 5,
            'verify' => false,
            'http_errors' => false,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
        ]))->post('https://sc.ftqq.com/'.$key.'.send', [
            'form_params' => [
                'text' => $text,
                'desp' => $desc,
            ],
        ]);

        return json_decode($response->getBody()->getContents(), true);
    }
}

if (!function_exists('cache_config')) {
    /**
     * 获取Admin Config的缓存键的值
     *
     * @param $key
     * @return mixed
     */
    function cache_config($key)
    {
        return \Illuminate\Support\Facades\Cache::get(\App\Models\Admin\AdminConfig::CACHE_KEY_PREFIX.$key);
    }
}

if (!function_exists('admin_switch_arr')) {
    /**
     * admin系统的switch选项.
     *
     * @param      $arr
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

if (!function_exists('is_wechat')) {
    /**
     * 判断是否是微信访问.
     *
     * @return bool
     */
    function is_wechat()
    {
        return false !== strpos(\Jenssegers\Agent\Facades\Agent::getUserAgent(), 'MicroMessenger');
    }
}
