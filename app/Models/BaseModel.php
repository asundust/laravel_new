<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseModel.
 *
 * @property mixed $status_name
 * @mixin Eloquent
 */
class BaseModel extends Model
{
    use BaseModelTrait;

    protected $guarded = [];

    const STATUS = [
        0 => '无效',
        1 => '有效',
    ];

    /**
     * 为数组 / JSON 序列化准备日期。
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
