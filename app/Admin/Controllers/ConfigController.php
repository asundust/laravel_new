<?php

namespace App\Admin\Controllers;

use App\Models\Admin\AdminConfig;
use Carbon\Carbon;
use Encore\Admin\Config\ConfigController as BaseConfigController;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class ConfigController extends BaseConfigController
{
    use HasResourceActions;

    /**
     * 列表.
     */
    public function index(Content $content): Content
    {
        return $content
            ->header('网站配置')
            ->description('列表&nbsp;<span class="small">[默认缓存时间为'.AdminConfig::CACHE_TTL.'秒]</span>')
            ->body($this->grid());
    }

    /**
     * 编辑.
     *
     * @param int $id
     */
    public function edit($id, Content $content): Content
    {
        return $content
            ->header('网站配置')
            ->description('编辑')
            ->body($this->form()->edit($id));
    }

    /**
     * 创建.
     */
    public function create(Content $content): Content
    {
        return $content
            ->header('网站配置')
            ->description('创建')
            ->body($this->form());
    }

    /**
     * 详情.
     *
     * @param $id
     */
    public function show($id, Content $content): Content
    {
        return $content
            ->header('网站配置')
            ->description('详情')
            ->body($this->detail($id));
    }

    /**
     * 列表字段.
     */
    public function grid(): Grid
    {
        $grid = new Grid(new AdminConfig());

        $grid->column('id', 'ID')->sortable()->hide();
        $grid->column('name', '名称')->display(function ($name) {
            return "<a tabindex=\"0\" class=\"btn btn-xs btn-twitter\" role=\"button\" data-toggle=\"popover\" data-html=true title=\"用法\" data-content=\"<code>cache_config('$name');</code>\">$name</a>";
        });
        $grid->column('value', '值')->editable();
        $grid->column('cache_value', '缓存值')->display(function () {
            return Cache::get(AdminConfig::CACHE_KEY_PREFIX.$this->name);
        })->help('如果与“值”不一致，点击“刷新配置缓存”');
        $grid->column('description', '描述');
        $grid->column('sort', '排序')->sortable();

        $grid->column('created_at', '创建时间')->display(function ($createdAt) {
            return Carbon::parse($createdAt)->toDateTimeString();
        })->hide();
        $grid->column('updated_at', '更新时间')->display(function ($updatedAt) {
            return Carbon::parse($updatedAt)->toDateTimeString();
        })->hide();

        $grid->tools(function (Grid\Tools $tools) {
            $tools->append('<div class="btn-group"><a href="'.admin_url('config/refresh').'" class="btn btn-success btn-sm" title="刷新配置缓存"><i class="fa fa-refresh"></i><span class="hidden-xs">&nbsp;刷新配置缓存</span></a></div>');
        });

        $grid->filter(function ($filter) {
            $filter->disableIdFilter();
            $filter->like('name', '名称');
            $filter->like('value', '值');
            $filter->like('description', '描述');
        });

        $grid->model()->orderBy('sort');

        return $grid;
    }

    /**
     * 详情字段.
     *
     * @param $id
     */
    protected function detail($id): Show
    {
        $show = new Show(AdminConfig::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('name', '名称');
        $show->field('value', '值');
        $show->field('description', '描述');
        $show->field('sort', '排序');
        $show->field('created_at', '创建时间');
        $show->field('updated_at', '更新时间');

        return $show;
    }

    /**
     * 表单字段.
     */
    public function form(): Form
    {
        $form = new Form(new AdminConfig());

        $form->display('id', 'ID');
        $form->display('name', '名称');
        $form->textarea('value', '值');
        $form->display('description', '描述');
        $form->display('sort', '排序')->default(0);

        // $form->display('created_at', '创建时间');
        // $form->display('updated_at', '更新时间');

        return $form;
    }

    /**
     * 刷新配置缓存.
     */
    public function refresh(): RedirectResponse
    {
        AdminConfig::configLoad();
        admin_toastr('配置缓存刷新成功');

        return back();
    }
}
