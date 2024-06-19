<?php

if (!function_exists('domain')) {
    /**
     * 获取当前域名
     *
     * @return string
     */
    function domain(): string
    {
        return parse_url(request()->url())['host'];
    }
}

if (!function_exists('safe_bc_dev')) {
    /**
     * 可以传入 0 的 bcdiv
     *
     * @param $dividend
     * @param $divisor
     * @param $scale
     *
     * @return int|string|null
     */
    function safe_bc_dev($dividend, $divisor, $scale = 2)
    {
        if (!$dividend || !$divisor) {
            return 0;
        }

        return bcdiv($dividend, $divisor, $scale);
    }
}

if (!function_exists('bc_array_sum')) {
    /**
     * 计算数组的和
     *
     * @param $array
     * @param $scale
     *
     * @return int|string
     */
    function bc_array_sum($array, $scale = 2)
    {
        $sum = 0;
        foreach ($array as $value) {
            $sum = bcadd($sum, $value, $scale);
        }
        return $sum;
    }
}

if (!function_exists('safe_explode')) {
    /**
     * 可传入数组的 explode
     *
     * @param $delimiter
     * @param $string
     *
     * @return array|false|string[]
     */
    function safe_explode($delimiter, $string)
    {
        if (is_array($string)) {
            return $string;
        }

        return explode($delimiter, $string);
    }
}

if (!function_exists('like')) {
    /**
     * 返回前后拼接了 % 的字符串
     *
     * @param $str
     *
     * @return string
     */
    function like($str)
    {
        return "%{$str}%";
    }
}

if (!function_exists('is_json')) {
    /**
     * 是否是json字符串
     *
     * @param $string
     *
     * @return bool
     */
    function is_json($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE);
    }
}

if (!function_exists('table_columns')) {
    /**
     * 获取表字段
     *
     * @param $tableName
     *
     * @return array
     */
    function table_columns($tableName)
    {
        return \Illuminate\Support\Facades\Schema::getColumnListing($tableName);
    }
}

if (!function_exists('file_to_line')) {
    /**
     * 一行行读取文件
     *
     * @param $path
     *
     * @return array|false
     */
    function file_to_line($path)
    {
        return file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    }
}

if (!function_exists('html_to_string')) {
    /**
     * html 转 string
     *
     * @param $content
     *
     * @return string
     */
    function html_to_string($content)
    {
        if (!$content) {
            return '';
        }
        $handle1 = htmlspecialchars_decode($content);   //把一些预定义的 HTML 实体转换为字符
        $handle2 = str_replace("&nbsp;", "", $handle1); //将空格替换成空

        return strip_tags($handle2);                    //函数剥去字符串中的 HTML、XML 以及 PHP 的标签,获取纯文本内容
    }
}

if (!function_exists('sql_record')) {
    /**
     * 获取 sql 执行记录
     *
     * @return array
     */
    function sql_record()
    {
        return \Slowlyo\Support\SqlRecord::$sql;
    }
}

if (!function_exists('make_dir')) {
    /**
     * 创建目录
     *
     * @param $path
     * @param $mode
     * @param $recursive
     * @param $context
     *
     * @return void
     */
    function make_dir($path, $mode = 0777, $recursive = true, $context = null)
    {
        if (!is_dir($path)) {
            @mkdir($path, $mode, $recursive, $context);
        }
    }
}
