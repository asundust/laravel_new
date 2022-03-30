<?php

namespace App\Http\Middleware;

use App\Models\Wechat\WechatUser;
use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Session;
use Overtrue\LaravelWeChat\Events\WeChatUserAuthorized;
use Overtrue\LaravelWeChat\Middleware\OAuthAuthenticate;

class WechatAuthMiddleware extends OAuthAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $account
     * @param string|null              $scope
     * @param string|null              $type    : service(服务号), subscription(订阅号), work(企业微信)
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $account = 'default', $scope = null, $type = 'service')
    {
        if (!is_wechat()) {
            return $next($request);
        }

        // 保证兼容性
        $class = ('work' !== $type) ? 'wechat' : 'work';
        $prefix = ('work' !== $type) ? 'official_account' : 'work';
        $sessionKey = \sprintf('%s.oauth_user.%s', $class, $account);
        $service = \sprintf('wechat.%s.%s', $prefix, $account);
        $config = config($service, []);
        $officialAccount = app($service);

        $scope = $scope ?: Arr::get($config, 'oauth.scopes', ['snsapi_base']);

        if (is_string($scope)) {
            $scope = array_map('trim', explode(',', $scope));
        }

        if (Session::has($sessionKey) && Session::has(WechatUser::SESSION_KEY)) {
            event(new WeChatUserAuthorized(session($sessionKey), false, $account));

            return $next($request);
        }

        // 是否强制使用 HTTPS 跳转
        $enforceHttps = Arr::get($config, 'oauth.enforce_https', false);

        if ($request->has('code')) {
            session([$sessionKey => $officialAccount->oauth->user()]);

            event(new WeChatUserAuthorized(session($sessionKey), true, $account));

            return redirect()->to($this->getTargetUrl($request, $enforceHttps));
        }

        session()->forget($sessionKey);
        session()->forget(WechatUser::SESSION_KEY);

        // 跳转到微信授权页
        return redirect()->away(
            $officialAccount->oauth->scopes($scope)->redirect($this->getRedirectUrl($request, $enforceHttps))->getTargetUrl()
        );
    }
}
