<?php

namespace App\Models;

use DateTimeInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseModel.
 *
 * @property mixed $status_string
 * @property mixed $created_at_format
 * @property mixed $updated_at_format
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
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
