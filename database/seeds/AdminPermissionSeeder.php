<?php

use Encore\Admin\Auth\Database\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AdminPermissionSeeder extends Seeder
{
    public static $permissionArr = [
        [
            'name' => '后台配置管理权限',
            'slug' => 'ext.config',
            'http_method' => '',
            'http_path' => 'config*',
        ],
        // [
        //     'name' => 'Scheduling管理权限',
        //     'slug' => 'ext.scheduling',
        //     'http_method' => '',
        //     'http_path' => 'scheduling*',
        // ],
        // [
        //     'name' => '后台日志权限',
        //     'slug' => 'ext.log-viewer',
        //     'http_method' => '',
        //     'http_path' => 'logs*',
        // ],
        // [
        //     'name' => '后台Redis管理权限',
        //     'slug' => 'ext.redis-manager',
        //     'http_method' => '',
        //     'http_path' => 'redis*',
        // ],
        // [
        //     'name' => 'Composer浏览权限',
        //     'slug' => 'ext.composer-viewer',
        //     'http_method' => '',
        //     'http_path' => 'composer-viewer*',
        // ],
    ];

    public static $permissionKeys1 = [
        'name', 'slug',
    ];

    public static $permissionKeys2 = [
        'http_method', 'http_path',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list1 = [
            [
                'name' => 'All permission',
                'slug' => '*',
                'http_path' => '*',
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'http_path' => '/',
            ],
            [
                'name' => 'Login',
                'slug' => 'auth.login',
                'http_path' => "/auth/login\r\n/auth/logout",
            ],
            [
                'name' => 'User setting',
                'slug' => 'auth.setting',
                'http_path' => '/auth/setting',
            ],
            [
                'name' => 'Auth management',
                'slug' => 'auth.management',
                'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ];

        $list2 = [
            [
                'name' => '最高权限',
                'slug' => '*',
                'http_path' => '*',
            ],
            [
                'name' => '后台主页权限',
                'slug' => 'dashboard',
                'http_path' => '/',
            ],
            [
                'name' => '登陆权限',
                'slug' => 'auth.login',
                'http_path' => "/auth/login\r\n/auth/logout",
            ],
            [
                'name' => '登陆用户设置权限',
                'slug' => 'auth.setting',
                'http_path' => '/auth/setting',
            ],
            [
                'name' => '权限管理权限',
                'slug' => 'auth.management',
                'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
        ];

        $count = 0;
        foreach ($list1 as $k => $list) {
            $result = Permission::where($list)->update($list2[$k]);
            if ($result != false) {
                $count++;
            }
        }
        console_comment('权限名称本地化完成：共' . $count . '条' . PHP_EOL);

        console_info('自定义权限开始处理');
        $count = 0;
        foreach (self::$permissionArr as $permission) {
            console_info('　　　　　当前处理：' . $permission['name'] . ' ' . $permission['slug']);
            $result = Permission::updateOrCreate(Arr::only($permission, self::$permissionKeys1), Arr::only($permission, self::$permissionKeys2));
            if ($result != false) {
                $count++;
            }
        }
        console_comment('　　　　　处理完成：共' . $count . '条' . PHP_EOL);
    }
}
