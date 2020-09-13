<?php

namespace App\Models\Admin;

use Eloquent;
use Encore\Admin\Config\ConfigModel;
use Illuminate\Database\Eloquent\Builder;
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
 *
 * @method static Builder|AdminConfig newModelQuery()
 * @method static Builder|AdminConfig newQuery()
 * @method static Builder|AdminConfig query()
 * @method static Builder|AdminConfig whereCreatedAt($value)
 * @method static Builder|AdminConfig whereDescription($value)
 * @method static Builder|AdminConfig whereId($value)
 * @method static Builder|AdminConfig whereName($value)
 * @method static Builder|AdminConfig whereSort($value)
 * @method static Builder|AdminConfig whereUpdatedAt($value)
 * @method static Builder|AdminConfig whereValue($value)
 * @mixin Eloquent
 */
class AdminConfig extends ConfigModel
{
    protected $fillable = ['name', 'value', 'description', 'sort'];
}
