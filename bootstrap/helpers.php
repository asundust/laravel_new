<?php

if (!function_exists('cache_config')) {
    /**
     * 获取Admin Config的缓存键的值
     *
     * @param $key
     * @param $default
     *
     * @return mixed
     */
    function cache_config($key, $default = null): mixed
    {
        return \Illuminate\Support\Facades\Cache::get(\App\Models\Admin\AdminConfig::CACHE_KEY_PREFIX . $key, $default);
    }
}

if (!function_exists('admin_switch_arr')) {
    /**
     * Laravel-Admin系统的switch选项.
     *
     * @param      $arr
     * @param bool $isOpposite
     *
     * @return array
     */
    function admin_switch_arr($arr, bool $isOpposite = true): array
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

if (!function_exists('admin_select_arr')) {
    /**
     * Laravel-Admin系统的select选项
     *
     * @param $items
     * @param string $key
     * @param string $value
     * @param array $suffixData
     * @return array
     */
    function admin_select_arr($items, string $key = 'id', string $value = 'name', array $suffixData = []): array
    {
        $data = [];
        foreach ($items as $item) {
            $suffix = '';
            if ($suffixData && count($suffixData) == 4) {
                $suffix .= $item[$suffixData[0]] == $suffixData[1] ? $suffixData[2] : $suffixData[3];
            }
            $data[$item[$key]] = $item[$value] . $suffix;
        }
        return $data;
    }
}

if (!function_exists('is_wechat')) {
    /**
     * 判断是否是微信访问.
     *
     * @return bool
     */
    function is_wechat(): bool
    {
        return str_contains(\Jenssegers\Agent\Facades\Agent::getUserAgent(), 'MicroMessenger');
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 是否手机
     *
     * @return bool
     */
    function is_mobile(): bool
    {
        $agent = new Jenssegers\Agent\Agent();
        return $agent->isMobile();
    }
}
