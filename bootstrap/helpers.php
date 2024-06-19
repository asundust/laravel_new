<?php

if (!function_exists('is_wechat')) {
    /**
     * 是否微信
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
     * @return bool
     */
    function is_mobile(): bool
    {
        $agent = new Jenssegers\Agent\Agent();

        return $agent->isMobile();
    }
}

if (!function_exists('url_to_uri')) {
    /**
     * 转成成URI
     *
     * @param $url
     * @param string $suffix
     * @return string
     */
    function url_to_uri($url, string $suffix = '!'): string
    {
        if (!$url) {
            return '';
        }
        $arr = parse_url($url);
        if (isset($arr['scheme']) && isset($arr['host'])) {
            $url = str_replace($arr['scheme'] . '://' . $arr['host'], '', $url);
            $url = preg_replace("/$suffix.*/", '', $url);
        }
        return trim($url, '/');
    }
}

if (!function_exists('uri_to_image_url')) {
    /**
     * 还原URL
     *
     * @param $uri
     * @return string
     */
    function uri_to_url($uri): string
    {
        if (!$uri) {
            return '';
        }
        return \Illuminate\Support\Facades\Storage::url($uri);
    }
}

if (!function_exists('dev_code')) {
    /**
     * 是否开发模式
     *
     * @return bool
     */
    function is_dev_mode(): bool
    {
        return request()->input('dev_mode_code') === config('app.dev_mode_code');
    }
}