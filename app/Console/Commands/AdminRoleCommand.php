<?php

namespace App\Console\Commands;

use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class AdminRoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:role-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '后台角色处理';

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
     */
    public function handle(): void
    {
        $this->info('自定义角色开始处理');
        $count = 0;
        $adminRoleData = config('services.admin_roles');
        foreach ($adminRoleData as $role) {
            $this->info('　　　　当前处理：' . $role['name'] . ' ' . $role['slug']);
            $result = Role::firstOrCreate(Arr::only($role, ['name', 'slug']));
            if (false !== $result) {
                if (count($role['permissions']) > 0) {
                    $result->permissions()->sync(Permission::whereIn('slug', $role['permissions'])->get());
                }
                $count++;
            }
        }
        $this->comment('　　　　　处理完成：共' . $count . '条' . PHP_EOL);
    }
}
