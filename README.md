# laravel-install

适用于laravel8.x

## 安装
> composer require learn/laravel-install --dev
## 自定义安装动作
> config()->set('install.driver', yourdriver::class);
> yourdriver::class 应该实现 \Laravel\Install\Contracts\DriverInterface 契约;
