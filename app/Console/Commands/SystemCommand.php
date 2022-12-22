<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SystemCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system {type?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统安装/更新';

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
     * @return false|void
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'ready':
                if (!app()->isLocal()) {
                    $this->error('非法环境');

                    return 0;
                }
                $this->language();
                $this->comment('准备发布完成');
                break;
            case 'install':
                if (file_exists(__DIR__.'/../../../install.lock')) {
                    $this->error('如需重装，请删除“install.lock”文件！');

                    return 0;
                }

                $this->keyGenerate();
                $this->adminInstall();
                $this->publishAdminAssets();
                $this->adminMinify();
                $this->adminConfig();
                $this->adminRbac();
                $this->queueRestart();

                file_put_contents('install.lock', 'Install on '.date('Y-m-d H:i:s'));
                $this->comment('安装完成:)');
                break;
            case 'update':
                $this->migrate();
                $this->publishAdminAssets();
                $this->adminMinify();
                $this->adminConfig();
                $this->adminRbac();
                $this->queueRestart();

                $this->comment('更新完成:)');
                break;

            default:
                $this->error('只允许type参数类型为“ready”、“install”、“update”');
                break;
        }
    }

    private function keyGenerate(): void
    {
        if ($this->laravel['config']['app.key']) {
            $this->comment('Laravel Key 已存在'.PHP_EOL);
        } else {
            Artisan::call('key:generate', [
                '--ansi' => true,
            ], $this->output);
            $this->comment('Laravel Key 已生成'.PHP_EOL);
        }
    }

    private function adminInstall(): void
    {
        Artisan::call('admin:install', [], $this->output);
        $this->comment('Admin安装完成'.PHP_EOL);
    }

    private function migrate(): void
    {
        Artisan::call('migrate', [], $this->output);
        $this->comment('数据库迁移完成'.PHP_EOL);
    }

    private function publishAdminAssets(): void
    {
        Artisan::call('view:clear');
        Artisan::call('vendor:publish', [
            '--tag' => 'laravel-admin-assets',
            '--force' => true,
        ], $this->output);
        $this->comment('Admin视图文件更新完成'.PHP_EOL);
    }

    private function adminMinify(): void
    {
        Artisan::call('admin:minify', [
            '--clear' => true,
        ], $this->output);
        Artisan::call('admin:minify', [], $this->output);
        $this->comment('Admin压缩资源更新完成'.PHP_EOL);
    }

    private function adminConfig(): void
    {
        Artisan::call('admin:config', [
            'type' => 'new',
        ], $this->output);
    }

    private function adminRbac(): void
    {
        Artisan::call('admin:permission-update', [], $this->output);
        Artisan::call('admin:role-update', [], $this->output);
        Artisan::call('admin:menu-update', [], $this->output);
    }

    private function queueRestart(): void
    {
        Artisan::call('queue:restart');
        $this->comment('队列已重启'.PHP_EOL);
    }

    private function language(): void
    {
        Artisan::call('vendor:publish', [
            '--tag' => 'laravel-admin-lang',
            '--force' => true,
        ], $this->output);
        $this->comment('Admin语言包更新完成'.PHP_EOL);

        Artisan::call('lang:publish', [
            'locales' => 'zh_CN',
            '--force' => true,
        ], $this->output);
        $this->comment('Laravel语言包更新完成'.PHP_EOL);
    }
}
