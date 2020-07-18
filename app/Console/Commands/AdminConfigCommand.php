<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AdminConfigCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:config {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin网站配置信息处理';

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
     * @throws \Exception
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'new':
                $result = $this->call('db:seed', [
                    '--class' => 'AdminConfigSeeder'
                ]);
                if ($result !== false) {
                    $this->info('Admin网站配置已同步');
                } else {
                    $this->error('Admin网站配置同步失败');
                }
                break;
            default:
                $this->error("只允许type参数类型为“new”");
                break;
        }
    }
}
