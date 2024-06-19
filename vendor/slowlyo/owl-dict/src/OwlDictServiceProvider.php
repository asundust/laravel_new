<?php

namespace Slowlyo\OwlDict;

use Slowlyo\OwlAdmin\Extend\ServiceProvider;
use Slowlyo\OwlAdmin\Renderers\SwitchControl;

class OwlDictServiceProvider extends ServiceProvider
{
    protected $menu = [
        [
            'title' => '数据字典',
            'url'   => '/admin_dict',
            'icon'  => 'fluent-mdl2:dictionary',
        ],
    ];

    public function register()
    {
        parent::register();

        $this->app->singleton('admin.dict', AdminDict::class);
    }

    public function settingForm()
    {
        return $this->baseSettingForm()->body([
            SwitchControl::make()->name('disabled_dict_type')->label('屏蔽数据字典类型管理'),
            SwitchControl::make()->name('disabled_dict_create')->label('屏蔽数据字典创建'),
            SwitchControl::make()->name('disabled_dict_delete')->label('屏蔽数据字典删除'),
        ]);
    }
}
