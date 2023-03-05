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
     */
    public function handle(): void
    {
        $configData = config('services.admin_configs');
        $type = $this->argument('type');
        switch ($type) {
            case 'new':
                $this->info('后台配置开始处理');
                foreach ($configData as $key => $value) {
                    $this->info('　　　　当前处理：' . $value['name'] . ' ' . $value['description']);
                    $config = AdminConfig::firstOrCreate(['name' => $value['name']], $value);
                    $config->update(['sort' => $key + 1, 'description' => $value['description']]);
                }
                $this->comment('　　　　处理完成' . PHP_EOL);
                break;
            case 'delete':
                $this->info('后台删除配置开始处理');
                $configIds = [];
                foreach ($configData as $value) {
                    $config = AdminConfig::where('name', $value['name'])->first();
                    if ($config) {
                        $configIds[] = $config->id;
                    }
                }
                $configs = AdminConfig::whereNotIn('id', $configIds)->get();
                $count = $configs->count();
                if (0 == $count) {
                    $this->comment('　　　　处理完成：无将要删除的配置' . PHP_EOL);
                } else {
                    foreach ($configs as $config) {
                        $this->info('　　正在删除：' . $config->id . ' ' . $config->name . ' [' . $config->description . ']' . ' ，配置值为：' . $config->value);
                        $config->delete();
                    }
                    $this->comment('　　　　处理完成：共删除配置' . $count . '个' . PHP_EOL);
                }
                break;
            default:
                console_error('只允许type参数类型为“new”、“delete”');
                break;
        }
    }
}
