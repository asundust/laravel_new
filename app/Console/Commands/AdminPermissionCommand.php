<?php

namespace App\Console\Commands;

use Encore\Admin\Auth\Database\Permission;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AdminPermissionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:permission-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '后台权限处理';

    /**
     * @var string[]
     */
    public static $permissionKeys1 = [
        'name', 'slug',
    ];

    /**
     * @var string[]
     */
    public static $permissionKeys2 = [
        'http_method', 'http_path',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
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
            if (false != $result) {
                ++$count;
            }
        }
        console_comment('权限名称本地化完成：共' . $count . '条' . PHP_EOL);

        console_info('自定义权限开始处理');
        $count = 0;
        $adminPermissionData = config('services.admin_permissions');
        foreach ($adminPermissionData as $permission) {
            console_info('　　　　　当前处理：' . $permission['name'] . ' ' . $permission['slug']);
            $result = Permission::updateOrCreate(Arr::only($permission, self::$permissionKeys1), Arr::only($permission, self::$permissionKeys2));
            if (false != $result) {
                ++$count;
            }
        }
        console_comment('　　　　　处理完成：共' . $count . '条' . PHP_EOL);
    }
}
