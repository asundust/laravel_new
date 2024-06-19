<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HigherOrderWhenProxy;

trait BaseModelTrait
{
    /**
     * created_at_format
     *
     * @comment 创建时间的格式化
     * @return string
     */
    public function getCreatedAtFormatAttribute(): string
    {
        return $this[self::CREATED_AT]->toDateTimeString();
    }

    /**
     * updated_at_format
     *
     * @comment 更新时间的格式化
     * @return string
     */
    public function getUpdatedAtFormatAttribute(): string
    {
        return $this[self::UPDATED_AT]->toDateTimeString();
    }

    /**
     * @comment 为数组 / JSON 序列化准备日期。
     *
     * @param DateTimeInterface $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }

    /**
     * @comment 通用like方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder|HigherOrderWhenProxy
     */
    public function scopeLike(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder|HigherOrderWhenProxy
    {
        if (!$inputField) {
            $inputField = $field;
        }
        $keyword = strtolower(request()->input($inputField));
        if (strlen($keyword)) {
            if (str_contains($field, '|')) {
                $fields = explode('|', $field);
            } else {
                $fields = [$field];
            }
            if ($relation) {
                if ($whereHasFunction == 'whereHasIn') {
                    return $builder->whereHasIn($relation, fn (Builder $builder) => $this->scopeLike($builder, $field, $inputField));
                } else {
                    return $builder->whereHas($relation, fn (Builder $builder) => $this->scopeLike($builder, $field, $inputField));
                }
            } else {
                return $builder->when(strlen($keyword), function (Builder $builder) use ($fields, $keyword) {
                    $builder->where(function (Builder $builder) use ($fields, $keyword) {
                        foreach ($fields as $key => $field) {
                            if ($key == 0) {
                                $builder->whereRaw("lower($field) like '%$keyword%'");
                            } else {
                                $builder->orWhereRaw("lower($field) like '%$keyword%'");
                            }
                        }
                    });
                });
            }
        }
        return $builder;
    }

    /**
     * @comment 通用等于方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    public function scopeEq(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        return $this->scopeCompare($builder, 'eq', $field, $inputField, $relation, $whereHasFunction);
    }

    /**
     * @comment 通用大于方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    public function scopeGt(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        return $this->scopeCompare($builder, 'gt', $field, $inputField, $relation, $whereHasFunction);
    }

    /**
     * @comment 通用大于等于方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    public function scopeGte(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        return $this->scopeCompare($builder, 'gte', $field, $inputField, $relation, $whereHasFunction);
    }

    /**
     * @comment 通用小于方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    public function scopeLt(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        return $this->scopeCompare($builder, 'lt', $field, $inputField, $relation, $whereHasFunction);
    }

    /**
     * @comment 通用小于等于方法
     *
     * @param Builder $builder
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    public function scopeLte(Builder $builder, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        return $this->scopeCompare($builder, 'lte', $field, $inputField, $relation, $whereHasFunction);
    }

    /**
     * @comment 通用比较方法
     *
     * @param Builder $builder
     * @param string $compareType
     * @param string $field 数据库字段
     * @param string $inputField 入参字段
     * @param string $relation 关联方法
     * @param string $whereHasFunction whereHas方法
     * @return Builder
     */
    protected function scopeCompare(Builder $builder, string $compareType, string $field, string $inputField = '', string $relation = '', string $whereHasFunction = 'whereHasIn'): Builder
    {
        if (!$inputField) {
            $inputField = $field;
        }
        $keyword = request()->input($inputField);
        if (strlen($keyword)) {
            if ($relation) {
                if ($whereHasFunction == 'whereHasIn') {
                    return $builder->whereHasIn($relation, fn (Builder $builder) => $this->scopeCompare($builder, $compareType, $field, $inputField));
                } else {
                    return $builder->whereHas($relation, fn (Builder $builder) => $this->scopeCompare($builder, $compareType, $field, $inputField));
                }
            } else {
                switch ($compareType) {
                    case 'eq':
                        return $builder->where($field, $keyword);
                    case 'gt':
                        return $builder->where($field, '>', $keyword);
                    case 'gte':
                        return $builder->where($field, '>=', $keyword);
                    case 'lt':
                        return $builder->where($field, '<', $keyword);
                    case 'lte':
                        return $builder->where($field, '<=', $keyword);
                }
            }
        }
        return $builder;
    }
}
