<?php

namespace Slowlyo\OwlDict;

use Slowlyo\OwlAdmin\Extend\ServiceProvider;

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
            amis()->SwitchControl('disabled_dict_type', '屏蔽数据字典类型管理'),
            amis()->SwitchControl('disabled_dict_create', '屏蔽数据字典创建'),
            amis()->SwitchControl('disabled_dict_delete', '屏蔽数据字典删除'),
            amis()->SwitchControl('muggle_mode', '麻瓜模式')->labelRemark('开启后, 字典值将由系统随机生成'),
        ]);
    }
}
