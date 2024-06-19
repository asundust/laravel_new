<?php

namespace Slowlyo\OwlAdmin\Support\Cores;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Database
{
    private string|null $moduleName;

    public function __construct($moduleName = null)
    {
        $this->moduleName = $moduleName;
    }

    public static function make($moduleName = null)
    {
        return new self($moduleName);
    }

    public function tableName($name)
    {
        return $this->moduleName . $name;
    }

    public function create($tableName, $callback)
    {
        Schema::create($this->tableName($tableName), $callback);
    }

    public function dropIfExists($tableName)
    {
        Schema::dropIfExists($this->tableName($tableName));
    }

    public function initSchema()
    {
        $this->down();
        $this->up();
    }

    public function up()
    {
        $this->create('admin_users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 120)->unique();
            $table->string('password', 80);
            $table->tinyInteger('enabled')->default(1);
            $table->string('name')->default('');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });

        $this->create('admin_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->timestamps();
        });

        $this->create('admin_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique();
            $table->string('slug', 50)->unique();
            $table->text('http_method')->nullable();
            $table->text('http_path')->nullable();
            $table->integer('custom_order')->default(0);
            $table->integer('parent_id')->default(0);
            $table->timestamps();
        });

        $this->create('admin_menus', function (Blueprint $table) {
            $table->id();
            $table->integer('parent_id')->default(0);
            $table->integer('custom_order')->default(0);
            $table->string('title', 100)->comment('菜单名称');
            $table->string('icon', 100)->nullable()->comment('菜单图标');
            $table->string('url')->nullable()->comment('菜单路由');
            $table->tinyInteger('url_type')->default(1)->comment('路由类型(1:路由,2:外链,3:iframe)');
            $table->tinyInteger('visible')->default(1)->comment('是否可见');
            $table->tinyInteger('is_home')->default(0)->comment('是否为首页');
            $table->tinyInteger('keep_alive')->nullable()->comment('页面缓存');
            $table->string('iframe_url')->nullable()->comment('iframe_url');
            $table->string('component')->nullable()->comment('菜单组件');
            $table->tinyInteger('is_full')->default(0)->comment('是否是完整页面');
            $table->string('extension')->nullable()->comment('扩展');

            $table->timestamps();
        });

        $this->create('admin_role_users', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->index(['role_id', 'user_id']);
            $table->timestamps();
        });

        $this->create('admin_role_permissions', function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->index(['role_id', 'permission_id']);
            $table->timestamps();
        });

        $this->create('admin_permission_menu', function (Blueprint $table) {
            $table->integer('permission_id');
            $table->integer('menu_id');
            $table->index(['permission_id', 'menu_id']);
            $table->timestamps();
        });

        // 如果是模块，跳过下面的表
        if ($this->moduleName) {
            return;
        }

        $this->create('admin_code_generators', function (Blueprint $table) {
            $table->id();
            $table->string('title')->default('')->comment('名称');
            $table->string('table_name')->default('')->comment('表名');
            $table->string('primary_key')->default('id')->comment('主键名');
            $table->string('model_name')->default('')->comment('模型名');
            $table->string('controller_name')->default('')->comment('控制器名');
            $table->string('service_name')->default('')->comment('服务名');
            $table->longText('columns')->comment('字段信息');
            $table->tinyInteger('need_timestamps')->default(0)->comment('是否需要时间戳');
            $table->tinyInteger('soft_delete')->default(0)->comment('是否需要软删除');
            $table->text('needs')->nullable()->comment('需要生成的代码');
            $table->text('menu_info')->nullable()->comment('菜单信息');
            $table->text('page_info')->nullable()->comment('页面信息');
            $table->timestamps();
        });

        $this->create('admin_settings', function (Blueprint $table) {
            $table->string('key')->default('');
            $table->longText('values')->nullable();
            $table->timestamps();
        });

        $this->create('admin_extensions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->tinyInteger('is_enabled')->default(0);
            $table->timestamps();
        });

        $this->create('admin_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('页面名称');
            $table->string('sign')->comment('页面标识');
            $table->longText('schema')->comment('页面结构');
            $table->timestamps();
        });

        $this->create('admin_relationships', function (Blueprint $table) {
            $table->id();
            $table->string('model')->comment('模型');
            $table->string('title')->comment('关联名称');
            $table->string('type')->comment('关联类型');
            $table->string('remark')->comment('关联名称')->nullable();
            $table->text('args')->comment('关联参数')->nullable();
            $table->text('extra')->comment('额外参数')->nullable();
            $table->timestamps();
        });

        $this->create('admin_apis', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('接口名称');
            $table->string('path')->comment('接口路径');
            $table->string('template')->comment('接口模板');
            $table->tinyInteger('enabled')->default(1)->comment('是否启用');
            $table->longText('args')->comment('接口参数')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        $this->dropIfExists('admin_users');
        $this->dropIfExists('admin_roles');
        $this->dropIfExists('admin_permissions');
        $this->dropIfExists('admin_menus');
        $this->dropIfExists('admin_role_users');
        $this->dropIfExists('admin_role_permissions');
        $this->dropIfExists('admin_permission_menu');

        // 如果是模块，跳过下面的表
        if ($this->moduleName) {
            return;
        }

        $this->dropIfExists('admin_code_generators');
        $this->dropIfExists('admin_settings');
        $this->dropIfExists('admin_extensions');
        $this->dropIfExists('admin_pages');
        $this->dropIfExists('admin_relationships');
        $this->dropIfExists('admin_apis');
    }

    /**
     * 填充初始数据
     *
     * @return void
     */
    public function fillInitialData()
    {
        $data = function ($data) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k] = "['" . implode("','", $v) . "']";
                }
            }
            $now = date('Y-m-d H:i:s');

            return array_merge($data, ['created_at' => $now, 'updated_at' => $now]);
        };

        $adminUser       = DB::table($this->tableName('admin_users'));
        $adminMenu       = DB::table($this->tableName('admin_menus'));
        $adminPermission = DB::table($this->tableName('admin_permissions'));
        $adminRole       = DB::table($this->tableName('admin_roles'));

        // 创建初始用户
        $adminUser->truncate();
        $adminUser->insert($data([
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'name'     => 'Administrator',
        ]));

        // 创建初始角色
        $adminRole->truncate();
        $adminRole->insert($data([
            'name' => 'Administrator',
            'slug' => 'administrator',
        ]));

        // 用户 - 角色绑定
        DB::table($this->tableName('admin_role_users'))->truncate();
        DB::table($this->tableName('admin_role_users'))->insert($data([
            'role_id' => 1,
            'user_id' => 1,
        ]));

        // 创建初始权限
        $adminPermission->truncate();
        $adminPermission->insert([
            $data(['name' => '首页', 'slug' => 'home', 'http_path' => ['/home*'], "parent_id" => 0]),
            $data(['name' => '系统', 'slug' => 'system', 'http_path' => '', "parent_id" => 0]),
            $data(['name' => '管理员', 'slug' => 'admin_users', 'http_path' => ["/admin_users*"], "parent_id" => 2]),
            $data(['name' => '角色', 'slug' => 'roles', 'http_path' => ["/roles*"], "parent_id" => 2]),
            $data(['name' => '权限', 'slug' => 'permissions', 'http_path' => ["/permissions*"], "parent_id" => 2]),
            $data(['name' => '菜单', 'slug' => 'menus', 'http_path' => ["/menus*"], "parent_id" => 2]),
            $data(['name' => '设置', 'slug' => 'settings', 'http_path' => ["/settings*"], "parent_id" => 2]),
        ]);

        // 角色 - 权限绑定
        DB::table($this->tableName('admin_role_permissions'))->truncate();
        $permissionIds = DB::table($this->tableName('admin_permissions'))->orderBy('id')->pluck('id');
        foreach ($permissionIds as $id) {
            DB::table($this->tableName('admin_role_permissions'))->insert($data([
                'role_id'       => 1,
                'permission_id' => $id,
            ]));
        }

        // 创建初始菜单
        $adminMenu->truncate();
        $adminMenu->insert([
            $data([
                'parent_id' => 0,
                'title'     => 'dashboard',
                'icon'      => 'mdi:chart-line',
                'url'       => '/dashboard',
                'is_home'   => 1,
            ]),
            $data([
                'parent_id' => 0,
                'title'     => 'admin_system',
                'icon'      => 'material-symbols:settings-outline',
                'url'       => '/system',
                'is_home'   => 0,
            ]),
            $data([
                'parent_id' => 2,
                'title'     => 'admin_users',
                'icon'      => 'ph:user-gear',
                'url'       => '/system/admin_users',
                'is_home'   => 0,
            ]),
            $data([
                'parent_id' => 2,
                'title'     => 'admin_roles',
                'icon'      => 'carbon:user-role',
                'url'       => '/system/admin_roles',
                'is_home'   => 0,
            ]),
            $data([
                'parent_id' => 2,
                'title'     => 'admin_permission',
                'icon'      => 'fluent-mdl2:permissions',
                'url'       => '/system/admin_permissions',
                'is_home'   => 0,
            ]),
            $data([
                'parent_id' => 2,
                'title'     => 'admin_menu',
                'icon'      => 'ant-design:menu-unfold-outlined',
                'url'       => '/system/admin_menus',
                'is_home'   => 0,
            ]),
            $data([
                'parent_id' => 2,
                'title'     => 'admin_setting',
                'icon'      => 'akar-icons:settings-horizontal',
                'url'       => '/system/settings',
                'is_home'   => 0,
            ]),
        ]);

        // 权限 - 菜单绑定
        DB::table($this->tableName('admin_permission_menu'))->truncate();
        $menus = $adminMenu->get();
        foreach ($menus as $menu) {
            $_list   = [];
            $_list[] = $data(['permission_id' => $menu->id, 'menu_id' => $menu->id]);

            if ($menu->parent_id != 0) {
                $_list[] = $data(['permission_id' => $menu->parent_id, 'menu_id' => $menu->id]);
            }

            DB::table($this->tableName('admin_permission_menu'))->insert($_list);
        }

        // 默认中文
        settings()->set('admin_locale', 'zh_CN');
    }

    public static function getTables()
    {
        try {
            return collect(json_decode(json_encode(Schema::getAllTables()), true))
                ->map(fn($i) => config('database.default') == 'sqlite' ? $i['name'] : array_shift($i))
                ->toArray();
        } catch (\Throwable $e) {
        }

        // laravel 11+
        return array_column(Schema::getTables(), 'name');
    }
}
