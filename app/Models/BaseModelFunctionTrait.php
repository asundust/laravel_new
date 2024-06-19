<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait BaseModelFunctionTrait
{
    /**
     * status_string
     *
     * @comment 状态名称
     * @return string
     */
    public function getStatusStringAttribute(): string
    {
        return self::STATUS[$this['status']] ?? '';
    }

    /**
     * 创建不重复新订单号.
     */
    public static function getNewNumber(string $fieldName = 'no'): string
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
     * @param bool|string $appendNull 是否添加空选项
     * @param string|null $key 键名
     * @param string|null $value 键值
     * @param array $suffixData 后缀选项 ['status', 1, '[无效]', '[]'] [字段名, 等于判断值, 真返回值, 假返回值]
     * @return array
     */
    public static function selectArr(bool|string|null $appendNull = null, string|null $key = null, string|null $value = null, array $suffixData = []): array
    {
        $value ??= 'name';
        $key ??= 'id';
        $appendNull ??= '无';
        $cacheKey = get_called_class() . self::SELECT_ARR_KEY . $key . $value . md5(implode('|', $suffixData));

        return Cache::remember($cacheKey, 60, function () use ($appendNull, $suffixData, $key, $value) {
            $data = [];
            if ($appendNull !== false) {
                $data[''] = $appendNull;
            }
            foreach (self::get() as $model) {
                $suffix = '';
                if ($suffixData && count($suffixData) == 4) {
                    $suffix = $model[$suffixData[0]] == $suffixData[1] ? $suffixData[2] : $suffixData[3];
                }
                $data[$model[$key]] = $model[$value] . $suffix;
            }

            return $data;
        });
    }
}
