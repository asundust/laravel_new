<?php

namespace Slowlyo\OwlAdmin\Extend;

use Illuminate\Support\Arr;
use Slowlyo\OwlAdmin\Admin;
use Slowlyo\OwlAdmin\Renderers\Form;
use Illuminate\Support\Facades\Route;
use Psr\Container\NotFoundExceptionInterface;
use Slowlyo\OwlAdmin\Support\ComposerProperty;
use Psr\Container\ContainerExceptionInterface;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

abstract class ServiceProvider extends LaravelServiceProvider
{
    use CanImportMenu;

    /**
     * @var ComposerProperty
     */
    public $composerProperty;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $packageName;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var array
     */
    protected $exceptRoutes = [];

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface|\ReflectionException
     */
    public function boot()
    {
        if (Extension::tableExists()) {
            $this->autoRegister();

            $this->init();
        }
    }

    /**
     * 初始化操作.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException|\Exception
     */
    final public function init()
    {
        $this->customInitBefore();

        if ($this->disabled()) {
            return;
        }

        if ($lang = $this->getLangPath()) {
            $this->loadTranslationsFrom($lang, $this->getName());
        }

        if ($this->middleware()) {
            $this->addMiddleware();
        }

        if ($this->exceptRoutes) {
            $this->addExceptRoutes();
        }

        $this->initRoutes();

        $this->customInitAfter();
    }

    public function customInitBefore()
    {
    }

    public function customInitAfter()
    {

    }

    public function settingForm()
    {
        return null;
    }

    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    public function initRoutes()
    {
        if ($this->disabled()) {
            return;
        }

        if ($routes = $this->getRoutes()) {
            $this->registerRoutes($routes);
        }
    }

    /**
     * 自动注册扩展.
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function autoRegister()
    {
        if ($this->getName()) {
            return;
        }

        Admin::extension()->addExtension($this);
    }

    /**
     * 获取扩展名称.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name ?: ($this->name = str_replace('/', '.', $this->getPackageName()));
    }

    /**
     * 获取扩展别名.
     *
     * @return mixed|null
     */
    public function getAlias()
    {
        return $this->composerProperty?->alias;
    }

    /**
     * 获取包名.
     *
     * @return string|void
     */
    public function getPackageName()
    {
        if (!$this->packageName) {
            if (!$this->composerProperty) {
                return;
            }

            $this->packageName = $this->composerProperty->name;
        }

        return $this->packageName;
    }

    /**
     * 获取当前已安装版本.
     *
     * @return string
     */
    public function getVersion()
    {
        if (!$this->composerProperty) {
            return '1.0.0';
        }

        return $this->composerProperty->version;
    }

    /**
     * 获取扩展包路径.
     *
     * @param string|null $path
     *
     * @return string
     * @throws \Exception
     */
    public function path(?string $path = null)
    {
        if (!$this->path) {
            $this->path = realpath(dirname((new \ReflectionClass(static::class))->getFileName()) . '/..');

            if (!is_dir($this->path)) {
                throw new \Exception("The {$this->path} is not a directory.");
            }
        }

        $path = ltrim($path, '/');

        return $path ? $this->path . '/' . $path : $this->path;
    }

    /**
     * 获取logo路径.
     *
     * @return string
     *
     * @throws \ReflectionException|\Exception
     */
    public function getLogoPath()
    {
        return $this->path('logo.png');
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getLogoBase64()
    {
        try {
            $logo = $this->getLogoPath();

            if (is_file($logo) && $file = fopen($logo, 'rb', 0)) {
                $content = fread($file, filesize($logo));
                fclose($file);
                $base64 = chunk_split(base64_encode($content));

                return 'data:image/png;base64,' . $base64;
            }
        } catch (\ReflectionException $e) {
        }

        return '';
    }

    /**
     * 获取扩展文档.
     *
     * @return false|string|void
     * @throws \Exception
     */
    public function getDocs()
    {
        $docs = ['readme.md', 'README.md'];
        foreach ($docs as $str) {
            $path = $this->path($str);

            if (is_file($path)) {
                return file_get_contents($path);
            }
        }
    }

    /**
     * 判断扩展是否启用.
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function enabled()
    {
        return Admin::extension()->enabled($this->getName());
    }

    /**
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    public function used()
    {
        return Admin::extension()->settings()->has($this->getName());
    }

    /**
     * 判断扩展是否禁用.
     *
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function disabled()
    {
        return !$this->enabled();
    }

    /**
     * 获取或保存配置.
     *
     * @param string|null $key
     * @param mixed        $default
     *
     * @return mixed
     */
    public function config(string $key = null, $default = null)
    {
        $config = Admin::setting()->get($this->getConfigKey());

        if (is_null($key)) {
            return $config;
        }

        return Arr::get($config, $key, $default);
    }

    /**
     * @param $key
     * @param $default
     *
     * @return mixed|null
     */
    public static function setting($key = null, $default = null)
    {
        $extension = static::instance();

        if ($extension instanceof ServiceProvider) {
            return $extension->config($key, $default);
        }

        return null;
    }

    /**
     * 保存配置.
     *
     * @param array $config
     */
    public function saveConfig(array $config)
    {
        Admin::setting()->set($this->getConfigKey(), $config);
    }

    /**
     * 初始化配置.
     */
    protected function initConfig()
    {
        $this->config = Admin::setting()->get($this->getConfigKey());
    }

    /**
     * 启用.
     *
     * @return void
     * @throws \Exception
     */
    public function doEnable($enable = true)
    {
        if ($enable) {
            $this->refreshMenu();
        } else {
            $this->flushMenu();
        }
    }

    /**
     * 执行迁移
     *
     * @param bool $down
     *
     * @return void
     * @throws \Exception
     */
    public function runMigrations(bool $down = false)
    {
        $path = $this->path('database/migrations');

        if (!is_dir($path)) {
            return;
        }

        if ($down) {
            app('migrator')->rollback($path);
        } else {
            app('migrator')->run($path);
        }
    }

    /**
     * 安装
     *
     * @return void
     * @throws \Exception
     */
    public function install()
    {
        $this->publishable();
        $this->runMigrations();
    }

    /**
     * 卸载扩展.
     *
     * @return void
     * @throws \Exception
     */
    public function uninstall()
    {
        $this->flushMenu();
        $this->unpublishable();
        $this->runMigrations(true);
        \Slowlyo\OwlAdmin\Models\Extension::query()->where('name', $this->getName())->delete();
    }

    /**
     * 发布静态资源.
     *
     * @return void
     * @throws \Exception
     */
    public function publishable()
    {
        if (file_exists($this->getAssetPath())) {
            if (!file_exists($this->getPublishPath())) {
                app('files')->makeDirectory($this->getPublishPath(), 0755, true, true);
            }
            app('files')->copyDirectory($this->getAssetPath(), $this->getPublishPath());
        }
    }

    /**
     * 取消发布静态资源.
     *
     * @return void
     */
    public function unpublishable()
    {
        app('files')->deleteDirectory($this->getPublishPath());
    }

    /**
     * 获取资源发布路径.
     *
     * @return string
     */
    protected function getPublishPath()
    {
        return public_path('extensions/' . $this->getPackageName());
    }

    /**
     * 获取静态资源路径.
     *
     * @return string
     * @throws \Exception
     */
    final public function getAssetPath()
    {
        return $this->path('public/extensions/' . $this->packageName);
    }


    /**
     * 获取语言包路径.
     *
     * @return string
     * @throws \Exception
     */
    final public function getLangPath()
    {
        return $this->path('lang');
    }

    /**
     * 注册路由.
     *
     * @param $callback
     */
    public function registerRoutes($callback)
    {
        Route::group(array_filter([
            'domain'     => Admin::config('admin.route.domain'),
            'prefix'     => Admin::config('admin.route.prefix'),
            'middleware' => Admin::config('admin.route.middleware'),
        ]),
            $callback);
    }

    /**
     * 获取中间件.
     *
     * @return array
     */
    protected function middleware()
    {
        return $this->middleware;
    }

    /**
     * 注册中间件.
     */
    protected function addMiddleware()
    {
        $this->mixMiddleware($this->middleware());
    }

    protected function mixMiddleware(array $middle)
    {
        Admin::mixMiddlewareGroup($middle);
    }

    /**
     * 配置需要跳过权限认证和登录认证的路由.
     */
    protected function addExceptRoutes()
    {
        if (!empty($this->exceptRoutes['permission'])) {
            Admin::context()->merge('permission.except', (array)$this->exceptRoutes['permission']);
        }

        if (!empty($this->exceptRoutes['auth'])) {
            Admin::context()->merge('auth.except', (array)$this->exceptRoutes['auth']);
        }
    }

    /**
     * 获取路由地址.
     *
     * @return string
     *
     * @throws \ReflectionException|\Exception
     */
    final public function getRoutes()
    {
        $path = $this->path('src/Http/routes.php');

        return is_file($path) ? $path : null;
    }

    /**
     * @param ComposerProperty $composerProperty
     *
     * @return $this
     */
    public function withComposerProperty(ComposerProperty $composerProperty)
    {
        $this->composerProperty = $composerProperty;

        return $this;
    }

    /**
     * 翻译.
     *
     * @param string $key
     * @param array  $replace
     * @param mixed   $locale
     *
     * @return array|string|null
     */
    public static function trans(string $key, array $replace = [], $locale = null)
    {
        return trans(static::instance()->getName() . '::' . $key, $replace, $locale);
    }

    /**
     * 获取自身实例.
     *
     * @return $this
     */
    public static function instance()
    {
        return app(static::class);
    }

    /**
     * 配置key.
     *
     * @return string|string[]
     */
    protected function getConfigKey()
    {
        return str_replace('.', ':', $this->getName());
    }

    protected function baseSettingForm()
    {
        return Form::make()
            ->panelClassName('border-0')
            ->affixFooter()
            ->title('')
            ->data(['extension' => $this->getName()])
            ->initApi([
                'url'    => admin_url('dev_tools/extensions/get_config'),
                'method' => 'POST',
                'data'   => [
                    'extension' => $this->getName(),
                ],
            ])
            ->actions([amis('submit')->label(admin_trans('admin.save'))->level('primary')])
            ->api('post:' . admin_url('dev_tools/extensions/save_config'));
    }

    public function assetUrl($path)
    {
        return url('extensions/' . $this->packageName . $path);
    }

    public function loadJs($path)
    {
        Admin::js($this->assetUrl($path));
    }

    public function loadCss($path)
    {
        Admin::css($this->assetUrl($path));
    }
}
