<?php

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class AdminRoleSeeder extends Seeder
{
    public static $roleArr = [
        // [
        //     'name' => '角色名',
        //     'slug' => 'role.role_name',
        //     'permissions' => [
        //         'permission.role_name',
        //     ],
        // ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        console_info('自定义角色开始处理');
        $count = 0;
        foreach (self::$roleArr as $role) {
            console_info('　　　　当前处理：' . $role['name'] . ' ' . $role['slug']);
            $result = Role::firstOrCreate(Arr::only($role, ['name', 'slug']));
            if ($result != false) {
                if (count($role['permissions']) > 0) {
                    $result->permissions()->sync(Permission::whereIn('slug', $role['permissions'])->get());
                }
                $count++;
            }
        }
        console_comment('　　　　　处理完成：共' . $count . '条' . PHP_EOL);
    }
}
