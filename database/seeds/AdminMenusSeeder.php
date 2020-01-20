<?php

use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;

class AdminMenusSeeder extends Seeder
{
    private $startOrder = 3;

    // icon图标去这里找 http://demo.test/admin/auth/menu
    // type 0=>默认的laravel-admin链接，1=>本站内的链接，2=>外部链接(需带http头)，
    private $data
        = [
            [
                'title' => '网站管理',
                'icon' => 'fa-cog',
                'type' => 0,
                'uri' => '',
                'permission' => '',
                'roles' => [

                ],
                'data' => [
                    [
                        'title' => '网站配置',
                        'icon' => 'fa-toggle-on',
                        'type' => 0,
                        'uri' => 'config',
                        'permission' => '',
                        'roles' => [

                        ],
                    ],
                ]
            ],
            [
                'title' => '系统工具',
                'icon' => 'fa-cog',
                'type' => 0,
                'uri' => '',
                'permission' => '',
                'roles' => [

                ],
                'data' => [
                    [
                        'title' => 'Scheduling',
                        'icon' => 'fa-clock-o',
                        'type' => 0,
                        'uri' => 'scheduling',
                        'permission' => '',
                        'roles' => [

                        ],
                    ],
                    [
                        'title' => '日志浏览',
                        'icon' => 'fa-database',
                        'type' => 0,
                        'uri' => 'logs',
                        'permission' => '',
                        'roles' => [

                        ],
                    ],
                    [
                        'title' => 'Redis管理',
                        'icon' => 'fa-database',
                        'type' => 0,
                        'uri' => 'redis',
                        'permission' => '',
                        'roles' => [

                        ],
                    ],
                    [
                        'title' => 'Composer浏览',
                        'type' => 0,
                        'icon' => 'fa-gears',
                        'uri' => 'composer-viewer',
                        'permission' => '',
                        'roles' => [

                        ],
                    ],
                ]
            ],
        ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = new Menu;

        // add default menus.
        $menu->truncate();

        // 默认菜单中文化
        $menu->insert([
            [
                'parent_id' => 0,
                'order' => 1,
                'title' => '首页',
                'icon' => 'fa-bar-chart',
                'uri' => '/',
            ],
            [
                'parent_id' => 0,
                'order' => 2,
                'title' => '后台管理',
                'icon' => 'fa-tasks',
                'uri' => '',
            ],
            [
                'parent_id' => 2,
                'order' => 3,
                'title' => '用户管理',
                'icon' => 'fa-users',
                'uri' => 'auth/users',
            ],
            [
                'parent_id' => 2,
                'order' => 4,
                'title' => '角色管理',
                'icon' => 'fa-user',
                'uri' => 'auth/roles',
            ],
            [
                'parent_id' => 2,
                'order' => 5,
                'title' => '权限管理',
                'icon' => 'fa-ban',
                'uri' => 'auth/permissions',
            ],
            [
                'parent_id' => 2,
                'order' => 6,
                'title' => '菜单管理',
                'icon' => 'fa-bars',
                'uri' => 'auth/menu',
            ],
            [
                'parent_id' => 2,
                'order' => 7,
                'title' => '访问日志',
                'icon' => 'fa-history',
                'uri' => 'auth/logs',
            ],
        ]);

        foreach ($menu->whereIn('id', [2, 3, 4, 5, 6, 7])->get() as $menu) {
            $menu->roles()->sync(Role::where('slug', 'administrator')->pluck('id')->toArray());
        }

        // 自定义菜单
        console_info('开始处理菜单');
        foreach ($this->data as $parentKey => $parentValue) {
            $uriParent = $this->getUri($parentValue);
            console_info('当前处理父菜单：' . $parentValue['title'] . ' ' . $uriParent);
            $menuParent = $menu->where(
                'parent_id', 0)
                ->where('title', $parentValue['title'])
                ->where('uri', $uriParent)
                ->first();
            if (empty($menuParent)) {
                $menuParent = $menu->create([
                    'parent_id' => 0,
                    'order' => ++$this->startOrder,
                    'title' => $parentValue['title'],
                    'icon' => $parentValue['icon'],
                    'uri' => $uriParent,
                    'permission' => $parentValue['permission'],
                ]);
                if (count($parentValue['roles'])) {
                    $menuParent->roles()->sync(Role::whereIn('slug', $parentValue['roles'])->pluck('id')->toArray());
                }
            }

            foreach ($parentValue['data'] as $k => $v) {
                $uriChild = $this->getUri($v);
                console_info('　　　　子菜单：' . $v['title'] . ' ' . $uriChild);
                $menuChild = $menu->where('parent_id', $menuParent->id)
                    ->where('title', $v['title'])
                    ->where('uri', $uriChild)
                    ->first();
                if (empty($menuChild)) {
                    $menuChild = $menu->create([
                        'parent_id' => $menuParent->id,
                        'order' => $k + 1,
                        'title' => $v['title'],
                        'icon' => $v['icon'],
                        'uri' => $uriChild,
                        'permission' => $v['permission']
                    ]);
                    if (count($v['roles'])) {
                        $menuChild->roles()->sync(Role::whereIn('slug', $v['roles'])->pluck('id')->toArray());
                    }
                }
            }
        }

        // // laravel 5.7 装的开发插件 Telescope
        // if (app()->isLocal() && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
        //     $menu->create([
        //         'parent_id' => 0,
        //         'order' => ++$this->startOrder,
        //         'title' => 'Telescope',
        //         'icon' => 'fa-wrench',
        //         'uri' => url('/') . '/telescope',
        //     ]);
        // }

        console_comment('　　　　处理完成' . PHP_EOL);
    }

    /**
     * 获取URI
     *
     * @param $value
     * @return string
     */
    private function getUri($value)
    {
        switch ($value['type']) {
            case 1:
                return url('/') . (substr($value['uri'], 0, 1) == '/' ? $value['uri'] : '/' . $value['uri']);
                break;
            // case 2:
            //     return $value['uri'];
            //     break;

            default:
                return $value['uri'];
                break;
        }
    }
}
