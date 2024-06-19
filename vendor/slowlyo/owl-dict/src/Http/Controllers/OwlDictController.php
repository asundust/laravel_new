<?php

namespace Slowlyo\OwlDict\Http\Controllers;

use Slowlyo\OwlDict\OwlDictServiceProvider;
use Slowlyo\OwlDict\Services\AdminDictService;
use Slowlyo\OwlAdmin\Controllers\AdminController;

/**
 * @property \Slowlyo\OwlAdmin\Services\AdminService|AdminDictService $service
 */
class OwlDictController extends AdminController
{
    protected string $serviceName = AdminDictService::class;

    public function index()
    {
        if ($this->actionOfGetData()) {
            return $this->response()->success($this->service->list());
        }

        $css = [
            '.cxd-Tree-itemArrowPlaceholder' => ['display' => 'none'],
            '.cxd-Tree-itemLabel'            => ['padding-left' => '0 !important'],
        ];

        $page = amis()->Page()->body([
            amis()->Flex()->items([$this->navBar(), $this->list()]),
        ])->css($css);

        return $this->response()->success($page);
    }

    public function navBar()
    {
        $formItems = [
            amis()->TextControl('value', $this->trans('field.value'))->required()->maxLength(255),
            amis()->TextControl('key', $this->trans('field.key'))->required()->maxLength(255),
            amis()->SwitchControl('enabled', $this->trans('field.enabled'))->value(1),
        ];

        return amis()->Card()->className('w-1/4 mr-5 mb-0 min-w-xs')->body([
            amis()->Flex()->className('mb-4')->justify('space-between')->items([
                amis()->Wrapper()
                    ->size('none')
                    ->body($this->trans('dict_type'))
                    ->className('flex items-center text-md'),
            ]),
            amis()->Form()
                ->wrapWithPanel(false)
                ->body(
                    amis()->TreeControl('dict_type')
                        ->id('dict_type_list')
                        ->source('/admin_dict/dict_type_options')
                        ->set('valueField', 'id')
                        ->set('labelField', 'value')
                        ->showIcon(false)
                        ->searchable()
                        ->set('rootCreateTip', __('admin.create') . $this->trans('dict_type'))
                        ->selectFirst()
                        ->creatable($this->dictTypeEnabled())
                        ->addControls($formItems)
                        ->editable($this->dictTypeEnabled())
                        ->editControls(array_merge($formItems, [amis()->HiddenControl()->name('id')]))
                        ->removable($this->dictTypeEnabled())
                        ->addApi($this->getStorePath())
                        ->editApi($this->getUpdatePath())
                        ->deleteApi($this->getDeletePath())
                        ->menuTpl('<span class="${!enabled ? "text-gray-300" : ""} w-1/5">${value}</div>')
                        ->onEvent([
                            'change' => [
                                'actions' => [
                                    [
                                        'actionType' => 'url',
                                        'args'       => ['url' => '/admin_dict?dict_type=${dict_type}'],
                                    ],
                                ],
                            ],
                        ])
                ),
        ]);
    }

    /**
     * @return \Slowlyo\OwlAdmin\Renderers\Page
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function list()
    {
        $crud = $this->baseCRUD()
            ->syncLocation(false)
            ->api($this->getListGetDataPath() . '&parent_id=${dict_type || ' . $this->service->getFirstId() . '}&page=${page}&perPage=${perPage}&enabled=${enabled}&key=${key}&value=${value}')
            ->headerToolbar([
                $this->createButton(true)->visible(!OwlDictServiceProvider::setting('disabled_dict_create')),
                'bulkActions',
                amis('reload')->align('right'),
                amis('filter-toggler')->align('right'),
            ])
            ->bulkActions([
                $this->bulkDeleteButton()->visible(!OwlDictServiceProvider::setting('disabled_dict_delete')),
            ])
            ->filter(
                $this->baseFilter()->body([
                    amis()->TextControl('key', $this->trans('field.key'))->size('md'),
                    amis()->TextControl('value', $this->trans('field.value'))->size('md'),
                    amis()->SelectControl('enabled', $this->trans('field.enabled'))
                        ->size('md')
                        ->clearable()
                        ->options([
                            ['label' => $this->trans('yes'), 'value' => 1],
                            ['label' => $this->trans('no'), 'value' => 0],
                        ]),
                ])
            )
            ->columns([
                amis()->TableColumn('value', $this->trans('field.value')),
                amis()->TableColumn('key', $this->trans('field.key')),
                amis()->TableColumn('enabled', $this->trans('field.enabled'))->quickEdit(
                    amis()->SwitchControl()->mode('inline')->saveImmediately(true)
                ),
                amis()->TableColumn('sort', $this->trans('field.sort'))->width(120),
                amis()->TableColumn('created_at', __('admin.created_at'))->width(120),
                $this->rowActions([
                    $this->rowEditButton(true),
                    $this->rowDeleteButton()->visible(!OwlDictServiceProvider::setting('disabled_dict_delete')),
                ])->set('width', 240),
            ]);

        return $this->baseList($crud);
    }

    public function form()
    {
        return $this->baseForm()->id('dict_item_form')->data([
            'enabled' => true,
            'sort'    => 0,
        ])->body([
            amis()->SelectControl('parent_id', $this->trans('type'))
                ->source(admin_url('/admin_dict/dict_type_options'))
                ->clearable()
                ->required()
                ->value('${dict_type || ' . $this->service->getFirstId() . '}')
                ->valueField('id')
                ->labelField('value'),
            amis()->TextControl('value', $this->trans('field.value'))->required()->maxLength(255),
            amis()->TextControl('key', $this->trans('field.key'))->required()->maxLength(255)->addOn(
                amis()->VanillaAction()->label($this->trans('random'))->icon('fa-solid fa-shuffle')->onEvent([
                    'click' => [
                        'actions' => [
                            [
                                'actionType'  => 'setValue',
                                'componentId' => 'dict_item_form',
                                'args'        => [
                                    'value' => [
                                        'key' => '${PADSTART(INT(RAND()*1000000000), 9, "0") | base64Encode | lowerCase}',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ])
            ),
            amis()->NumberControl('sort', $this->trans('field.sort'))
                ->displayMode('enhance')
                ->min(0)
                ->max(9999)
                ->description($this->trans('sort_description')),
            amis()->SwitchControl('enabled', $this->trans('field.enabled')),
        ]);
    }

    public function dictTypeOptions()
    {
        return $this->response()->success($this->service->getDictTypeOptions());
    }

    public function detail($id)
    {
        return $this->baseDetail($id);
    }

    private function trans($key)
    {
        return OwlDictServiceProvider::trans('admin-dict.' . $key);
    }

    private function dictTypeEnabled()
    {
        return !OwlDictServiceProvider::setting('disabled_dict_type');
    }
}
