<?php

use Encore\Admin\Config\ConfigModel;
use Illuminate\Database\Seeder;

class AdminConfigSeeder extends Seeder
{
    public static $configArr = [
        [
            'description' => '是否强制HTTPS请求(0否，1是)',
            'name' => 'force_https',
            'value' => '0',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        console_info('后台配置开始处理');
        foreach (self::$configArr as $key => $v) {
            console_info('　　　　当前处理：' . $v['name'] . ' ' . $v['description']);
            $config = ConfigModel::firstOrCreate(['name' => $v['name']], $v);
            $config->update(['sort' => $key + 1, 'description' => $v['description']]);
        }
        console_comment('　　　　处理完成' . PHP_EOL);
    }
}
