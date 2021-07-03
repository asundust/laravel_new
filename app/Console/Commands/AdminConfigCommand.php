<?php

namespace App\Console\Commands;

use App\Models\Admin\AdminConfig;
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
        $adminConfigData = config('services.admin_configs');
        $type = $this->argument('type');
        switch ($type) {
            case 'new':
                console_info('后台配置开始处理');
                foreach ($adminConfigData as $key => $v) {
                    console_info('　　　　当前处理：'.$v['name'].' '.$v['description']);
                    $config = AdminConfig::firstOrCreate(['name' => $v['name']], $v);
                    $config->update(['sort' => $key + 1, 'description' => $v['description']]);
                }
                console_comment('　　　　处理完成'.PHP_EOL);
                break;
            case 'delete':
                console_info('后台删除配置开始处理');
                $configIds = [];
                foreach ($adminConfigData as $key => $v) {
                    $config = AdminConfig::where('name', $v['name'])->first();
                    if ($config) {
                        $configIds[] = $config->id;
                    }
                }
                $configs = AdminConfig::whereNotIn('id', $configIds)->get();
                $count = $configs->count();
                if (0 == $count) {
                    console_comment('　　　　处理完成：无将要删除的配置'.PHP_EOL);
                } else {
                    foreach ($configs as $config) {
                        console_info('　　正在删除：'.$config->id.' '.$config->name.' ['.$config->description.']'.' ，配置值为：'.$config->value);
                        $config->delete();
                    }
                    console_comment('　　　　处理完成：共删除配置'.$count.'个'.PHP_EOL);
                }
                break;
            default:
                console_error('只允许type参数类型为“new”、“delete”');
                break;
        }
    }
}
