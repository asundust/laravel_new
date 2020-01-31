<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 *
 * @package App\Models
 * @method status_name
 *
 * @property $id
 */
class BaseModel extends Model
{
    use BaseModelTrait;

    protected $guarded = [];

    const STATUS = [
        0 => '无效',
        1 => '有效',
    ];
}
