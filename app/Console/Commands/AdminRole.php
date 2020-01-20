<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdminRole extends Command
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
     *
     * @return mixed
     */
    public function handle()
    {
        $result = $this->call('db:seed', [
            '--class' => 'AdminRoleSeeder'
        ]);
        if ($result !== false) {
            $this->info('Admin后台角色处理成功');
        } else {
            $this->info('Admin后台角色处理失败');
        }
    }
}
