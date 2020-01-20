<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package App\Models
 * @method status_name
 *
 * @property $id
 */
class BaseModel extends Model
{
    use BaseModelTrait;

    const STATUS_FALSE = 0; // 假状态
    const STATUS_TRUE = 1; // 真状态

    protected $guarded = [];

    public static $statusArr = [
        0 => '无效',
        1 => '有效',
    ];
}
