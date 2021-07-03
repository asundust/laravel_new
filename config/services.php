<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Admin命令行执行的自动化配置
    // 后台配置
    'admin_configs' => [
        [
            'description' => '网站首页标题(默认“Laravel”)',
            'name' => 'index_title',
            'value' => '',
        ],
        [
            'description' => '消息发送通道(1.Server酱 2.Server酱Turbo版 3.企业微信消息推送(只走默认配置))',
            'name' => 'message_send_way',
            'value' => '',
        ],
        [
            'description' => 'Server酱推送密钥(请关注“方糖”公众号)',
            'name' => 'sc_send_key',
            'value' => '',
        ],
        [
            'description' => 'Server酱(Turbo版)推送密钥(自行配置好消息通道)',
            'name' => 'sct_send_key',
            'value' => '',
        ],
        [
            'description' => '企业微信消息推送推送用户(不填写默认“@all”)',
            'name' => 'wechat_work_push_user',
            'value' => '',
        ],
    ],
    // Admin后台菜单
    // icon图标去这里找 http://demo.test/admin/auth/menu
    // type 0=>默认的laravel-admin链接，1=>本站内的链接，2=>外部链接(需带http头)，
    'admin_menus' => [
        [
            'title' => '网站管理',
            'icon' => 'fa-cog',
            'type' => 0,
            'uri' => '',
            'permission' => '',
            'roles' => [
            ],
            'data' => [
                [
                    'title' => '网站配置',
                    'icon' => 'fa-toggle-on',
                    'type' => 0,
                    'uri' => 'config',
                    'permission' => '',
                    'roles' => [
                    ],
                ],
            ],
        ],
        [
            'title' => '企业微信消息推送',
            'icon' => 'fa-wechat',
            'type' => 0,
            'uri' => '',
            'permission' => '',
            'roles' => [
            ],
            'data' => [
                [
                    'title' => '用户配置',
                    'icon' => 'fa-users',
                    'type' => 0,
                    'uri' => 'wechatWorkPushUsers',
                    'permission' => '',
                    'roles' => [
                    ],
                ],
                [
                    'title' => '默认配置',
                    'icon' => 'fa-toggle-on',
                    'type' => 0,
                    'uri' => 'wechatWorkPushConfig',
                    'permission' => '',
                    'roles' => [
                    ],
                ],
            ],
        ],
    ],
    // Admin后台权限
    'admin_permissions' => [
        [
            'name' => '后台配置管理权限',
            'slug' => 'ext.config',
            'http_method' => '',
            'http_path' => 'config*',
        ],
    ],
    // Admin后台角色
    'admin_roles' => [
        // [
        //     'name' => '角色名',
        //     'slug' => 'role.role_name',
        //     'permissions' => [
        //         'permission.role_name',
        //     ],
        // ],
    ],
];
