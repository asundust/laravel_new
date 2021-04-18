<?php

namespace App\Models;

trait BaseModelTrait
{
    /**
     * 创建不重复新订单号.
     *
     * @param string $model
     * @param string $fieldName
     *
     * @return string
     */
    public static function getNewNumber($model = '', $fieldName = 'number'): string
    {
        $number = now()->format('ymdHis').rand(10000, 99999);
        if (empty($model)) {
            if (self::findNumber($number, '', $fieldName)) {
                return self::getNewNumber();
            }
        } else {
            if ((new $model())::findNumber($number, $model, $fieldName)) {
                return (new $model())::getNewNumber();
            }
        }

        return $number;
    }

    /**
     * 根据订单号查询订单.
     *
     * @param        $number
     * @param string $model
     * @param string $fieldName
     *
     * @return mixed
     */
    public static function findNumber($number, $model = '', $fieldName = 'number')
    {
        if (empty($model)) {
            return self::where($fieldName, $number)->first();
        }

        return (new $model())::where($fieldName, $number)->first();
    }

    // 状态名称 status_string
    public function getStatusStringAttribute(): ?string
    {
        return self::STATUS[$this->status] ?? '';
    }

    // 创建时间的格式化 created_at_format
    public function getCreatedAtFormatAttribute()
    {
        return $this->created_at->toDateTimeString();
    }

    // 创建时间的格式化 updated_at_format
    public function getUpdatedAtFormatAttribute()
    {
        return $this->updated_at->toDateTimeString();
    }
}
