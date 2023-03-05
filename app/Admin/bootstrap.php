<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 *
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 */
Encore\Admin\Form::forget(['map', 'editor']);

// select 汉化
Admin::js('/vendor/laravel-admin/AdminLTE/plugins/select2/select2.full.min.js');
Admin::js('/vendor/laravel-admin/AdminLTE/plugins/select2/i18n/zh-CN.js');

// Grid::init(function (Grid $grid) {
//     $grid->actions(function (Grid\Displayers\Actions $actions) {
//         $actions->disableDelete();
//         $actions->disableView();
//     });
//     $grid->tools(function (Grid\Tools $tools) {
//         $tools->batch(function (Grid\Tools\BatchActions $actions) {
//             $actions->disableDelete();
//         });
//     });
//     $grid->disableExport();
//     $grid->disableCreateButton();
// });
// Form::init(function (Form $form) {
//     $form->disableViewCheck();
//     $form->tools(function (Form\Tools $tools) {
//         $tools->disableDelete();
//         $tools->disableView();
//     });
// });