<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Cache;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slowlyo\OwlAdmin\Admin;
use Slowlyo\OwlAdmin\Events\ExtensionChanged;
use Slowlyo\OwlAdmin\Support\Cores\Database;
use Slowlyo\OwlDict\Services\AdminDictService;
use function Laravel\Prompts\select;

class SystemCommand extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system {action : 操作类型}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '系统安装/升级/资源更新';

    /**
     * Execute the console command.
     *
     * @return true|void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle()
    {
        switch ($this->argument('action')) {
            case 'source':
                if (!app()->isLocal()) {
                    $this->error('非法环境');
                    return true;
                }
                $this->language();
                $this->adminSourceUpdate();
                $this->comment('资源更新完成:)');
                break;
            case 'install':
                if (file_exists(__DIR__ . '/../../../install.lock')) {
                    $this->error('如需重装，请删除“install.lock”文件！');
                    return true;
                }
                $this->keyGenerate();
                $this->migrate();
                $this->adminInstall();
                $this->adminDictInstall();
                $this->adminDictUpdate();
                $this->queueRestart();

                file_put_contents('install.lock', 'Install on ' . date('Y-m-d H:i:s'));
                $this->comment('系统安装完成:)');
                break;
            case 'update':
                $this->migrate();
                $this->adminDictUpdate();
                $this->queueRestart();

                $this->comment('系统升级完成:)');
                break;
            default:
                $this->error('Action参数错误');
                return true;
        }
    }

    /**
     * @return mixed
     */
    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'action' => fn() => select(
                label: '选择一个操作',
                options: [
                    'source' => '升级资源',
                    'install' => '系统安装',
                    'update' => '系统升级',
                ],
                validate: ['action' => 'required|in:source,install,update'],
            ),
        ];
    }


    private function keyGenerate(): void
    {
        if ($this->laravel['config']['app.key']) {
            $this->comment('Laravel Key 已存在' . PHP_EOL);
        } else {
            $this->call('key:generate', [
                '--ansi' => true,
            ]);
            $this->comment('Laravel Key 已生成' . PHP_EOL);
        }
    }

    private function adminInstall(): void
    {
        if (Admin::adminUserModel()::query()->count() == 0) {
            Database::make()->fillInitialData();
        }
        $this->comment('Admin安装完成' . PHP_EOL);
    }

    private function migrate(): void
    {
        $this->call('migrate', []);
        $this->comment('数据库迁移完成' . PHP_EOL);
    }

    private function adminSourceUpdate(): void
    {
        $this->call('admin:publish', [
            '--assets' => true,
            '--lang' => true,
            '--force' => true,
        ]);
        $this->comment('Admin更新完成' . PHP_EOL);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function adminDictInstall(): void
    {
        $adminDictName = 'slowlyo.owl-dict';
        Admin::extension()->enable($adminDictName);
        ExtensionChanged::dispatch($adminDictName, 'enable');
        $data = [
            'disabled_dict_create' => true,
            'disabled_dict_delete' => true,
        ];
        Admin::extension($adminDictName)->saveConfig($data);
    }

    public function adminDictUpdate(): void
    {
        $this->call('admin:dict');
        Cache::forget(AdminDictService::All_DICT_CACHE_KEY);
        Cache::forget(AdminDictService::VALID_DICT_CACHE_KEY);
        AdminDictService::make()->getAllData();
        AdminDictService::make()->getValidData();
        $this->comment('Admin Dict更新完成' . PHP_EOL);
    }

    private function queueRestart(): void
    {
        $this->call('queue:restart', []);
        $this->comment('队列已重启' . PHP_EOL);
    }

    private function language(): void
    {
        $this->call('lang:add', [
            'locales' => ['en', 'zh_CN'],
        ]);
        $this->comment('Laravel语言包更新完成' . PHP_EOL);
    }
}
