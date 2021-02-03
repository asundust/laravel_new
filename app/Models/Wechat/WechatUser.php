<?php

namespace App\Models\Wechat;

use App\Models\BaseModel;
use Eloquent;
use Illuminate\Support\Carbon;

/**
 * App\Models\Wechat\WechatUser.
 *
 * @property int         $id
 * @property int|null    $user_id           用户id
 * @property string      $wechat_openid     微信openid
 * @property string|null $name              名称
 * @property string|null $nickname          昵称
 * @property string|null $avatar            头像
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property mixed       $created_at_format
 * @property mixed       $status_name
 * @property mixed       $updated_at_format
 * @mixin Eloquent
 */
class WechatUser extends BaseModel
{
    const SESSION_KEY = 'wechat_user';
}
