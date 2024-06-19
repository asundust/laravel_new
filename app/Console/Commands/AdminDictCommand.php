<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Slowlyo\OwlDict\Models\AdminDict;

class AdminDictCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:dict';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Admin字典更新';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        AdminDict::unguard();
        $parentDict = config('services.admin_dict');
        $parentCount = count($parentDict);
        foreach ($parentDict as $parentKey => $parentItem) {
            $parentDict = AdminDict::query()->firstOrCreate([
                'parent_id' => 0,
                'key' => $parentItem['category'],
            ], [
                'enabled' => 1,
                'sort' => $parentCount - $parentKey,
                'value' => $parentItem['category'],
            ]);
            $childDict = $parentItem['children'] ?? [];
            $childCount = count($childDict);
            $this->line("父字典：{$parentDict['key']}");
            foreach ($childDict as $childKey => $childItem) {
                $childDict = AdminDict::query()->firstOrCreate([
                    'parent_id' => $parentDict['id'],
                    'key' => $childItem['key'],
                ], [
                    'enabled' => 1,
                    'sort' => $childCount - $childKey,
                    'value' => $childItem['value'],
                ]);
                $this->line("　字典：{$childDict['key']}");
            }
        }
    }
}
