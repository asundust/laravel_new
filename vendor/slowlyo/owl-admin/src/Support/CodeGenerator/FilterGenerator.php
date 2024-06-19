<?php

namespace Slowlyo\OwlAdmin\Support\CodeGenerator;

class FilterGenerator extends BaseGenerator
{
    public static array $filterMap = [
        'equal'           => '=',
        'not_equal'       => '!=',
        'gt'              => '>',
        'gte'             => '>=',
        'lt'              => '<',
        'lte'             => '<=',
        'starts_with'     => 'like%',
        'contains'        => '%like%',
        'ends_with'       => '%like',
        'not_contains'    => 'not %like%',
        'not_starts_with' => 'not like%',
        'not_ends_with'   => 'not %like',
        'in'              => 'in',
        'not_in'          => 'not in',
        'between'         => 'between',
        'not_between'     => 'not between',
        'is_null'         => 'is null',
        'is_not_null'     => 'is not null',
    ];

    public function renderComponent()
    {
        $filters = collect(data_get($this->model->columns, '*.list_filter.*', []))
            ->where('mode', 'input')
            ->values()
            ->toArray();

        if (blank($filters)) {
            return null;
        }

        $components = [];
        foreach ($filters as $filter) {
            $filterType = data_get($filter, 'filter.filter_type', 'TextControl');
            $item       = "amis()->{$filterType}('{$filter['input_name']}', '{$filter['input_label']}')";
            if ($property = data_get($filter, 'filter.filter_property')) {
                $item .= $this->buildComponentProperty($property);
            }

            $components[] = "\t\t\t\t" . $item . ',' . PHP_EOL;
        }

        $content = "\t\t\t->filter(\$this->baseFilter()->body([" . PHP_EOL;
        $content .= implode('', $components);
        $content .= "\t\t\t]))" . PHP_EOL;

        return $content;
    }

    public function renderQuery()
    {
        $list = [];

        foreach ($this->model->columns as $column) {
            $filters = data_get($column, 'list_filter.*', []);
            foreach ($filters as $filter) {
                $list[] = $this->queryItem($column, $filter);
            }
        }

        if (blank($list)) {
            return null;
        }

        $content = PHP_EOL;
        $content .= "\tpublic function searchable(\$query)" . PHP_EOL;
        $content .= "\t{" . PHP_EOL;
        $content .= "\t\tparent::searchable(\$query);" . PHP_EOL . PHP_EOL;
        $content .= collect($list)->map(fn($i) => "\t\t{$i};")->implode(PHP_EOL) . PHP_EOL;
        $content .= "\t}" . PHP_EOL;

        return $content;
    }

    private function queryItem($column, $filter)
    {
        if ($filter['mode'] == 'fixed') {
            $value = $filter['value'] ?? '';
            if (filled($value) && !in_array($value, ['true', 'false']) && !is_numeric($value)) {
                $value = "'{$value}'";
            }
        } else {
            $value = "\$this->request->input('{$filter['input_name']}')";
        }
        $name     = $column['name'];
        $arrValue = "safe_explode(',', $value)";

        $querySchema = match ($filter['type']) {
            'equal'           => "->where('{$name}', {$value})",
            'not_equal'       => "->where('{$name}', '!=', {$value})",
            'gt'              => "->where('{$name}', '>', {$value})",
            'gte'             => "->where('{$name}', '>=', {$value})",
            'lt'              => "->where('{$name}', '<', {$value})",
            'lte'             => "->where('{$name}', '<=', {$value})",
            'starts_with'     => "->where('{$name}', 'like', {$value} . '%')",
            'contains'        => "->where('{$name}', 'like', '%' . {$value} . '%')",
            'ends_with'       => "->where('{$name}', 'like', '%'. {$value})",
            'not_contains'    => "->where('{$name}', 'not like', '%' . {$value} . '%')",
            'not_starts_with' => "->where('{$name}', 'not like', {$value} . '%')",
            'not_ends_with'   => "->where('{$name}', 'not like', '%' . {$value})",
            'in'              => "->whereIn('{$name}', {$arrValue})",
            'not_in'          => "->whereNotIn('{$name}', {$arrValue})",
            'between'         => "->whereBetween('{$name}', {$arrValue})",
            'not_between'     => "->whereNotBetween('{$name}', {$arrValue})",
            'is_null'         => "->whereNull('{$name}')",
            'is_not_null'     => "->whereNotNull('{$name}')",
        };

        return match ($filter['mode']) {
            'fixed' => "\$query{$querySchema}",
            'input' => "\$query->when({$value}, fn(\$q) => \$q{$querySchema})",
        };
    }
}
