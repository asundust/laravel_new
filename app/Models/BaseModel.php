<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseModel
 *
 * @property-read mixed $status_name
 * @mixin \Eloquent
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
