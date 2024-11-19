<?php

declare(strict_types=1);

namespace ManoCode\CustomExtend\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Slowlyo\OwlDict\Models\AdminDict;

/**
 * @property \Symfony\Component\Console\Output\OutputInterface $output
 */
trait CanImportDict
{

    protected array $dictValidationRules = [
        'key' => 'required',
        'value' => 'required',
    ];

    /**
     * 获取字典节点.
     *
     * @return array
     */
    protected function dict()
    {
        return $this->dict;
    }

    /**
     * 添加字典.
     *
     * @param array $dict
     *
     * @throws \Exception
     */
    protected function addDict(array $dict = [])
    {
        $dict = $dict ?: $this->dict();

        if (!Arr::isAssoc($dict)) {
            foreach ($dict as $v) {
                $this->addDict($v);
            }

            return;
        }

        if (!$this->validateDict($dict)) {
            return;
        }

        $parentId = AdminDict::query()->insertGetId([
            'key' => $dict['key'],
            'value' => $dict['value'],
            'extension' => $this->getName(),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        if (isset($dict['keys']) && count($dict['keys']) >= 1) {
            AdminDict::query()->insert(collect($dict['keys'])->map(function ($item) use ($parentId, $dict) {
                return [
                    'parent_id' => $parentId,
                    'key' => $dict['key'],
                    'value' => $dict['value'],
                    'extension' => $this->getName(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            })->toArray());
        }

    }

    /**
     * 刷新字典.
     *
     * @throws \Exception
     */
    protected function refreshDict()
    {
        $this->flushDict();

        $this->addDict();
    }

    /**
     * 删除字典.
     */
    protected function flushDict()
    {
        AdminDict::query()
            ->where('extension', $this->getName())
            ->delete();
    }

    /**
     * 验证菜单字段格式是否正确.
     *
     * @param array $dict
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validateDict(array $dict)
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($dict, $this->dictValidationRules);

        if ($validator->passes()) {
            return true;
        }

        return false;
    }
}
