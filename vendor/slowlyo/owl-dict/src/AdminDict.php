<?php

namespace Slowlyo\OwlDict;

use Illuminate\Support\Arr;
use Slowlyo\OwlDict\Services\AdminDictService as Service;

class AdminDict
{
    private $data;

    public function key($default = '')
    {
        return Arr::get($this->data, 'key', $default);
    }

    public function value($default = '')
    {
        return Arr::get($this->data, 'value', $default);
    }

    public function options()
    {
        return collect($this->data)->values()->map(fn($item) => [
            'label' => $item['value'],
            'value' => $item['key'],
        ])->toArray();
    }

    public function mapValues()
    {
        return (object)collect($this->data)->values()->pluck('value', 'key')->toArray();
    }

    public function all($default = [])
    {
        return $this->data ?: $default;
    }

    public function get($path, $needAllData = false)
    {
        $originData = $needAllData ? Service::make()->getAllData() : Service::make()->getValidData();
        $this->data = Arr::get($originData, $path);
        return $this;
    }

    public function getValue($path, $default = '', $needAllData = true)
    {
        return $this->get($path, $needAllData)->value($default);
    }

    public function getKey($path, $default = '', $needAllData = true)
    {
        return $this->get($path, $needAllData)->key($default);
    }

    public function getAll($path, $default = [], $needAllData = true)
    {
        return $this->get($path, $needAllData)->all($default);
    }

    /**
     * 获取value=>label 结构的映射数据
     * @param $path
     * @param $needAllData
     * @return mixed[]
     */
    public function getMapValues($path, $needAllData = true)
    {
        return $this->get($path, $needAllData)->mapValues();
    }

    public function getOptions($path, $needAllData = true)
    {
        return $this->get($path, $needAllData)->options();
    }
}
