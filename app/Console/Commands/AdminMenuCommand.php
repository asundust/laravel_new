<?php

namespace App\Console\Commands;

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
            '--class' => 'AdminMenusSeeder'
        ]);
        if ($result !== false) {
            $this->info('导航菜单刷新成功');
        } else {
            $this->error('导航菜单刷新失败');
        }
    }
}