<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property mixed $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $created_at_format 创建时间的格式化
 * @property-read string $updated_at_format 更新时间的格式化
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Illuminate\Database\Eloquent\Builder|User compare(string $compareType, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用比较方法
 * @method static \Illuminate\Database\Eloquent\Builder|User eq(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用等于方法
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User gt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于方法
 * @method static \Illuminate\Database\Eloquent\Builder|User gte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用大于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|User like(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用like方法
 * @method static \Illuminate\Database\Eloquent\Builder|User lt(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于方法
 * @method static \Illuminate\Database\Eloquent\Builder|User lte(string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn') 通用小于等于方法
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use BaseModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
