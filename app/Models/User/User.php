<?php

namespace App\Models\User;

use App\Models\BaseModelTrait;
use DateTimeInterface;
use Eloquent;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * App\Models\User\User.
 *
 * @property int                                                   $id
 * @property string                                                $name
 * @property string                                                $email
 * @property Carbon|null                                           $email_verified_at
 * @property string                                                $password
 * @property string|null                                           $remember_token
 * @property Carbon|null                                           $created_at
 * @property Carbon|null                                           $updated_at
 * @property DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property int|null                                              $notifications_count
 * @mixin Eloquent
 */
class User extends Authenticatable implements JWTSubject
{
    use BaseModelTrait;
    use Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
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
