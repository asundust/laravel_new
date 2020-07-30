<?php

namespace App\Admin\Controllers;

use App\Models\Admin\AdminConfig;
use Encore\Admin\Config\ConfigController as BaseConfigController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class ConfigController extends BaseConfigController
{
    use HasResourceActions;

    /**
     * Index interface.
     *
     * @param Content $content
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->header('网站配置')
            ->description('列表')
            ->body($this->grid());
    }

    /**
     * Edit interface.
     *
     * @param int $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->header('网站配置')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        return $content
            ->header('网站配置')
            ->description('创建')
            ->body($this->form());
    }

    public function show($id, Content $content)
    {
        return $content
            ->header('网站配置')
            ->description('详情')
            ->body(Admin::show(AdminConfig()::findOrFail($id), function (Show $show) {
                $show->field('id', 'ID');
                $show->field('name', '名称');
                $show->field('value', '值');
                $show->field('description', '描述');
                $show->field('sort', '排序');
                // $show->created_at('创建时间');
                // $show->updated_at('更新时间');
            }));
    }

    public function grid()
    {
        $grid = new Grid(new AdminConfig());

        $grid->column('id', 'ID')->sortable();
        $grid->column('name', '名称')->display(function ($name) {
            return "<a tabindex=\"0\" class=\"btn btn-xs btn-twitter\" role=\"button\" data-toggle=\"popover\" data-html=true title=\"用法\" data-content=\"<code>config('$name');</code>\">$name</a>";
        });
        $grid->column('value', '值')->editable();
        $grid->column('description', '描述');
        $grid->column('sort', '排序');

        // $grid->created_at('创建时间');
        // $grid->updated_at('更新时间');

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '名称');
            $filter->like('value', '值');
            $filter->like('description', '描述');
        });

        $grid->model()->orderBy('sort');

        return $grid;
    }

    public function form()
    {
        $form = new Form(new AdminConfig());

        $form->display('id', 'ID');
        $form->display('name', '名称')->rules('required');
        $form->textarea('value', '值')->rules('required');
        $form->display('description', '描述');
        $form->display('sort', '排序')->default(0);

        // $form->display('created_at', '创建时间');
        // $form->display('updated_at', '更新时间');

        return $form;
    }
}
