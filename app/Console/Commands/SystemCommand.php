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
     * @return mixed
     */
    public function handle()
    {
        $type = $this->argument('type');
        switch ($type) {
            case 'ready':
                if (!app()->isLocal()) {
                    $this->error('非法环境');

                    return false;
                }
                $this->comment('准备发布完成');
                break;
            case 'install':
                if (file_exists(__DIR__ . '/../../../install.lock')) {
                    $this->error('如需重装，请删除“install.lock”文件！');

                    return false;
                }
                if (!$this->laravel['config']['app.key']) {
                    Artisan::call('key:generate --ansi');
                }
                $this->comment('Laravel Key 已生成' . PHP_EOL);
                if (!$this->laravel['config']['jwt.secret']) {
                    Artisan::call('jwt:secret-force');
                }
                $this->comment('JWT 密钥 已生成' . PHP_EOL);
                Artisan::call('admin:install');
                $this->comment('Admin安装完成' . PHP_EOL);
                Artisan::call('vendor:publish', [
                    '--tag' => 'laravel-admin-assets',
                    '--force' => true,
                ]);
                Artisan::call('admin:minify', [
                    '--clear' => true,
                ]);
                Artisan::call('admin:minify');
                // Artisan::call('vendor:publish', [
                //     '--tag' => 'laravel-admin-lang',
                //     '--force' => true
                // ]);
                $this->comment('Admin视图更新完成' . PHP_EOL);
                Artisan::call('admin:config', [
                    'type' => 'new',
                ]);
                Artisan::call('admin:permission-update');
                Artisan::call('admin:role-update');
                Artisan::call('admin:menu-update');
                Artisan::call('queue:restart');
                console_comment('队列已重启' . PHP_EOL);
                file_put_contents('install.lock', 'Install on ' . date('Y-m-d H:i:s'));
                $this->comment('安装完成:)');
                break;
            case 'update':
                Artisan::call('migrate');
                $this->comment('数据库迁移完成' . PHP_EOL);
                Artisan::call('view:clear');
                Artisan::call('vendor:publish', [
                    '--tag' => 'laravel-admin-assets',
                    '--force' => true,
                ]);
                Artisan::call('admin:minify', [
                    '--clear' => true,
                ]);
                Artisan::call('admin:minify');
                // Artisan::call('vendor:publish', [
                //     '--tag' => 'laravel-admin-lang',
                //     '--force' => true
                // ]);
                $this->comment('Admin视图更新完成' . PHP_EOL);
                Artisan::call('admin:config', [
                    'type' => 'new',
                ]);
                Artisan::call('admin:permission-update');
                Artisan::call('admin:role-update');
                Artisan::call('admin:menu-update');
                Artisan::call('queue:restart');
                console_comment('队列已重启' . PHP_EOL);
                $this->comment('更新完成:)');
                break;

            default:
                $this->error('只允许type参数类型为“install”、“update”');
                break;
        }
    }
}
