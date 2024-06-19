<?php

namespace App\Models;

use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

/**
 * App\Models\PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property array|null $abilities
 * @property \Illuminate\Support\Carbon|null $last_used_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_format 创建时间的格式化
 * @property-read string $updated_at_format 更新时间的格式化
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $tokenable
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken compare(string $compareType, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用比较方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken eq(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken gt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken gte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken like(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用like方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken lt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken lte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @mixin \Eloquent
 */
class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use BaseModelTrait;
}
