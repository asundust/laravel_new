<?php

namespace App\Models\Admin;

use Encore\Admin\Config\ConfigModel;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Admin\AdminConfig.
 *
 * @property int $id
 * @property string $name
 * @property string|null $value
 * @property string|null $description
 * @property int|null $sort
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin\AdminConfig newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin\AdminConfig newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin\AdminConfig query()
 * @mixin \Eloquent
 */
class AdminConfig extends ConfigModel
{
    protected $fillable = ['name', 'value', 'description', 'sort'];

    public const CACHE_KEY_PREFIX = 'admin_config_cache_';
    public const CACHE_TTL = 864000;

    /**
     * 配置缓存操作.
     */
    public static function configLoad()
    {
        $ttl = self::CACHE_TTL;
        $configs = self::all(['name', 'value']);
        Cache::put(self::CACHE_KEY_PREFIX, 1, $ttl);
        foreach ($configs as $config) {
            Cache::put(self::CACHE_KEY_PREFIX . $config['name'], $config['value'], $ttl);
            config([$config['name'] => $config['value']]);
        }
    }
}
