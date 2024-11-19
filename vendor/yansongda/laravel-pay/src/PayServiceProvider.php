<?php

declare(strict_types=1);

namespace Yansongda\LaravelPay;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Yansongda\Artful\Exception\ContainerException;
use Yansongda\Pay\Pay;

class PayServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Boot the service.
     *
     * @author yansongda <me@yansongda.cn>
     */
    public function boot()
    {
        if ($this->app instanceof Application && $this->app->runningInConsole()) {
            $this->publishes([
                dirname(__DIR__).'/config/pay.php' => config_path('pay.php'), ],
                'laravel-pay'
            );
        }

        if ($this->app instanceof LumenApplication) {
            $this->app->configure('pay');
        }
    }

	/**
	 * Register the service.
	 *
	 * @return void*
	 * @throws ContainerException
	 * @author yansongda <me@yansongda.cn>
	 *
	 */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/pay.php', 'pay');

        Pay::config(config('pay'));

        $this->app->singleton('pay.alipay', function () {
            return Pay::alipay();
        });

        $this->app->singleton('pay.wechat', function () {
            return Pay::wechat();
        });

        $this->app->singleton('pay.unipay', function () {
            return Pay::unipay();
        });

		$this->app->singleton('pay.douyin', function () {
			return Pay::douyin();
		});

		$this->app->singleton('pay.jsb', function () {
			return Pay::jsb();
		});
    }

    /**
     * Get services.
     *
     * @author yansongda <me@yansongda.cn>
     *
     * @return array
     */
    public function provides()
    {
        return ['pay.alipay', 'pay.wechat', 'pay.unipay', 'pay.douyin', 'pay.jsb'];
    }
}
