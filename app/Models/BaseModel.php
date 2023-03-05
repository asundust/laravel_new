<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseModel.
 *
 * @property string $created_at_format
 * @property string $status_string
 * @property string $updated_at_format
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    use BaseModelTrait;
    use HasFactory;

    protected $guarded = [];

    public const SELECT_ARR_KEY = '-selectArr';
    public const STATUS = [
        0 => '无效',
        1 => '有效',
    ];

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
     * created_at_format
     *
     * @comment 创建时间的格式化
     * @return string
     */
    public function getCreatedAtFormatAttribute(): string
    {
        return $this[self::CREATED_AT]->toDateTimeString();
    }

    /**
     * updated_at_format
     *
     * @comment 更新时间的格式化
     * @return string
     */
    public function getUpdatedAtFormatAttribute(): string
    {
        return $this[self::UPDATED_AT]->toDateTimeString();
    }

    /**
     * 为数组 / JSON 序列化准备日期。
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
