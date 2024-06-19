<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseModel
 *
 * @property-read string $created_at_format 创建时间的格式化
 * @property-read string $updated_at_format 更新时间的格式化
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel compare(string $compareType, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用比较方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel eq(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel gt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel gte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel like(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用like方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel lt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel lte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseModel query()
 * @mixin \Eloquent
 */
class BaseModel extends Model
{
    use BaseModelTrait;

    protected $guarded = [];

    public const SELECT_ARR_KEY = '-selectArr';
    public const STATUS = [
        0 => '无效',
        1 => '有效',
    ];
}
