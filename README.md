![StyleCI build status](https://github.styleci.io/repos/235053411/shield)

## 关于

[Laravel](https://github.com/laravel/laravel)本地化(中文化)项目模板，带[Owl-Admin](https://github.com/slowlyo/owl-admin)、

同步[github.com](https://github.com/asundust/laravel_new)和[gitee.com](https://gitee.com/asundust/laravel_new)

关于`Composer源`的声明：由于连接性的问题故使用的是`腾讯云Composer源`(本地全局更改，非项目更改)，故产生的`composer.lock`文件会有`腾讯云Composer源`的链接。

## **大道至简**

适配跟进速度跟不上Laravel框架迭代速度，取消了所有用不到的东西，只留下最基础的东西，保留了一些开发上可能需要的安装包。
具体可以查看`composer.json`文件可以查看安装了什么。

## 快速开始

复制`.env.example`到`.env`文件，在`.env`文件里配置好数据库配置，在命令行里执行`php artisan system install`即可。

## 使用

内置了vendor环境，如不需要，请去`.gitignore`内添加`/vendor`，在命令行里执行`git rm -r --cached ./vendor`，然后再提交到git库即可。

如果需要使用内置vendor环境，请在开发前更新一下环境，执行`composer update`即可。

一键安装/更新命令 `php artisan system install`，`php artisan system update`。

`php artisan system install`会在项目根目录产生`install.lock`文件，如果无权限，请自行更改命令代码。

建议查看一下`app/Console/Commands/SystemCommand.php`(`php artisan system`)逻辑，以便更好的使用该命令。

其他命令`php artisan`查看。

## ~~Demo(尚未完成，跳票中)~~

基于此项目写的一个简单收款和管理[Demo](https://pay.leeay.com/pay)，带有前台`/pay`和后台`/admin/orders`，由于限制，仅有部分支付功能

项目地址(同步[github.com/asundust/laravel_pay_demo](https://github.com/asundust/laravel_pay_demo)
和[gitee.com/asundust/laravel_pay_demo](https://gitee.com/asundust/laravel_pay_demo))

## License

[The MIT License (MIT)](https://opensource.org/licenses/MIT)
