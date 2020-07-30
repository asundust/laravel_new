<?php

namespace App\Console\Commands;

use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Console\Command;

class AdminMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:menu-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '更新后台导航菜单';

    /**
     * @var int
     */
    private $startOrder = 3;

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
        $menu = new Menu;

        // 清空菜单表
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
        $adminMenuData = config('services.admin_menus');
        foreach ($adminMenuData as $parentKey => $parentValue) {
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

            default:
                return $value['uri'];
                break;
        }
    }
}