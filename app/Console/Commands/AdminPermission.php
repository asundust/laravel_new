<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdminPermission extends Command
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
        $result = $this->call('db:seed', [
            '--class' => 'AdminPermissionSeeder'
        ]);
        if ($result !== false) {
            $this->info('Admin后台权限处理成功');
        } else {
            $this->info('Admin后台权限处理失败');
        }
    }
}
