# Laravel Support

php/laravel部分代码封装

## 安装

```shell
composer require slowlyo/laravel-support
```

## Macro

- findInSet
    - 查询支持 `find_in_set`
- toRawSql
    - 输出拼接了参数的原始sql

## Functions

```php
// functions start

// 获取当前域名function domain()

// 可以传入 0 的 bcdivfunction safe_bc_dev($dividend, $divisor, $scale = 2)

// 计算数组的和function bc_array_sum($array, $scale = 2)

// 可传入数组的 explodefunction safe_explode($delimiter, $string)

// 返回前后拼接了 % 的字符串function like($str)

// 是否是json字符串function is_json($string)

// 获取表字段function table_columns($tableName)

// 一行行读取文件function file_to_line($path)

// html 转 stringfunction html_to_string($content)

// 获取 sql 执行记录function sql_record()

// 创建目录function make_dir($path, $mode = 0777, $recursive = true, $context = null)

// 抽奖function lottery($probs)

// functions end
```
