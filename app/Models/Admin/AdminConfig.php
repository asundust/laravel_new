<?php

namespace App\Models\Admin;

use Encore\Admin\Config\ConfigModel;

class AdminConfig extends ConfigModel
{
    protected $fillable = ['name', 'value', 'description', 'sort'];
}
