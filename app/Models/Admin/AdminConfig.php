<?php

namespace App\Models\Admin;

use Eloquent;
use Encore\Admin\Config\ConfigModel;
use Illuminate\Support\Carbon;

/**
 * App\Models\Admin\AdminConfig.
 *
 * @property int         $id
 * @property string      $name
 * @property string      $value
 * @property string|null $description
 * @property int|null    $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @mixin Eloquent
 */
class AdminConfig extends ConfigModel
{
    protected $fillable = ['name', 'value', 'description', 'sort'];
}
