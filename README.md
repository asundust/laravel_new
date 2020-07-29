## 关于

[Laravel](https://github.com/laravel/laravel)本地化(中文化)项目模板，带[Laravel-Admin](https://github.com/z-song/laravel-admin)、[Config](https://github.com/laravel-admin-extensions/config)、一键安装更新命令、[支付宝微信支付](https://github.com/yansongda/laravel-pay)、支付Demo，已安装[dingo/api](https://github.com/dingo/api)扩展包以及部分其他辅助扩展包，目前[Laravel](https://github.com/laravel/laravel)版本是7.x版本。


同步[github.com](https://github.com/asundust/laravel_new)和[gitee.com](https://gitee.com/asundust/laravel_new)


关于`Composer源`的声明：由于连接性的问题故使用的是`阿里云Composer源`(本地全局更改，非项目更改)，故产生的`composer.lock`文件会有`阿里云Composer源`的链接。


## 快速开始

复制`.env.example`到`.env`文件，在`.env`文件里配置好数据库配置，在命令行里执行`php artisan system install`即可。


## 使用

内置了vendor环境，如不需要，请去`.gitignore`内添加`/vendor`，在命令行里执行`git rm -r --cached ./vendor`，然后再提交到git库即可。


一键安装/更新命令 `php artisan system install`，`php artisan system update`。

`php artisan system install`会在项目根目录产生`install.lock`文件，如果无权限，请自行更改命令代码。

建议查看一下`app/Console/Commands/System.php`(`php artisan system`)逻辑，以便更好的使用该命令。

目前问题：在`production`环境下无法正确执行询问形命令，目前尚无解决方法。

其他命令`php artisan`查看。

## Demo

基于此项目写的一个简单收款和管理[Demo](https://pay.leeay.com/pay)，带有前台`/pay`和后台`/admin/orders`，由于限制，仅有部分支付功能

项目地址(同步[github.com/asundust/laravel_pay_demo](https://github.com/asundust/laravel_pay_demo)和[gitee.com/asundust/laravel_pay_demo](https://gitee.com/asundust/laravel_pay_demo))


## 支付

内置了一个用于测试订单支付的`app/Models/Pay/DemoOrder`Model
参照下面的可以发起测试支付
```
use App\Http\Controllers\Service\Pay\AlipayService;
use App\Http\Controllers\Service\Pay\WechatPayService;
use App\Models\Pay\DemoOrder;
```
```
$order = DemoOrder::create([
    'user_id' => 0,
    'title' => '测试订单 - 充值0.01',
    'price' => '0.01',
]);
$bill = $order->bills()->create(['pay_no' => $order->number, 'title' => $order->title, 'amount' => $order->price, 'pay_way' => 2]);
return (new AlipayService())->pay(['no' => $bill->pay_no, 'amount' => $bill->amount, 'title' => $bill->title]);
$bill = $order->bills()->create(['pay_no' => $order->number, 'title' => $order->title, 'amount' => $order->price, 'pay_way' => 1]);
return (new WechatPayService())->pay(['no' => $bill->pay_no, 'amount' => $bill->amount, 'title' => $bill->title], 'scan');
```
如果不需要此Demo则需要删除`database/migrations/2020_09_02_000000_create_demo_orders_table.php`和`app/Models/Pay/DemoOrder.php`文件

## 许可证
[MIT](https://opensource.org/licenses/MIT)
