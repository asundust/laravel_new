<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait BaseModelTrait
{
    /**
     * 创建不重复新订单号.
     */
    public static function getNewNumber(string $fieldName = 'number'): string
    {
        $number = now()->format('ymdHis') . rand(10000, 99999);
        if (self::findNumber($number, $fieldName)) {
            return self::getNewNumber();
        }

        return $number;
    }

    /**
     * 根据订单号查询订单.
     */
    public static function findNumber(int|string $number, string $fieldName = 'number'): ?Model
    {
        return self::where($fieldName, $number)->first();
    }

    /**
     * 选项数字.
     *
     * @param  bool|string  $appendNull  是否添加空选项
     * @param  string  $key  键名
     * @param  string  $value  键值
     * @param  array  $suffixData  后缀选项 ['status', 1, '[无效]', []] [字段名, 等于判断值, 真返回值, 假返回值]
     */
    public static function selectArr(bool|string $appendNull = '无', string $key = 'id', string $value = 'name', array $suffixData = []): array
    {
        $cacheKey = get_called_class() . self::SELECT_ARR_KEY . $key . $value . md5(implode('|', $suffixData));

        return Cache::remember($cacheKey, 60, function () use ($appendNull, $key, $value, $suffixData) {
            $data = [];
            if (false !== $appendNull) {
                $data[''] = $appendNull;
            }
            foreach (self::get() as $model) {
                $suffix = '';
                if ($suffixData && 4 == count($suffixData)) {
                    $suffix = $model[$suffixData[0]] == $suffixData[1] ? $suffixData[2] : $suffixData[3];
                }
                $data[$model[$key]] = $model[$value] . $suffix;
            }

            return $data;
        });
    }
}
