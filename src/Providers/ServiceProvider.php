<?php
/*
|----------------------------------------------------------------------------
| TopWindow [ Internet Ecological traffic aggregation and sharing platform ]
|----------------------------------------------------------------------------
| Copyright (c) 2006-2019 http://yangrong1.cn All rights reserved.
|----------------------------------------------------------------------------
| Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
|----------------------------------------------------------------------------
| Author: yangrong <yangrong2@gmail.com>
|----------------------------------------------------------------------------
| ServiceProvider
|----------------------------------------------------------------------------
*/
namespace Learn\Install\Providers;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\Schema;
use Learn\Install\Support\Util;
use Learn\Install\Support\Driver;
use Learn\Install\Controllers\Posts;
use Learn\Install\Middleware\CheckWhetherTheSystemIsInstalled;
class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (!$this->app->config->get('app.debug')) {
            return;
        }
        $this->registerMiddleware(CheckWhetherTheSystemIsInstalled::class);
        $this->app->afterResolving(Posts::class, function ($posts, $app) {
            $driver = $app->make($app->config->get('install.driver', Driver::class));
            $posts->driver($driver);
        });
        $this->booting(function () {
            $source = \realpath($raw = \dirname(__DIR__, 2) . '/config/install.php') ?: $raw;
            if ($this->app->runningInConsole()) {
                $this->publishes([$source => \config_path('install.php')], 'laravel-install');
            }
            if (!$this->app->configurationIsCached()) {
                $this->mergeConfigFrom($source, 'install');
            }
        });
        $this->booted(function () {
            $this->app['router']->prefix('install')->middleware('web')->namespace('Learn\\Install\\Controllers')->group(function ($router) {
                $router->get('install_protocols.html', 'ShowHtml@useProtocol')->name('install.step1');
                $router->get('environmental_detection.html', 'ShowHtml@environmentalDetection')->name('install.step2');
                $router->get('parameter_setting.html', 'ShowHtml@parameterSetting')->name('install.step3');
                $router->get('installation_is_complete.html', 'ShowHtml@installationIsComplete')->name('install.step5');
                $router->post('service.html', 'Posts@service')->name('install.service');
                $router->get('static/assets/{path}', 'ShowStatic@show')->where('path', '[\\w\\.\\/\\-_]+')->name('install.static');
            });
            $this->app['router']->getRoutes()->refreshNameLookups();
            $this->app['router']->getRoutes()->refreshActionLookups();
        });
    }
    /**
     * Register the Middleware
     *
     * @param  string $middleware
     */
    protected function registerMiddleware($middleware)
    {
        $kernel = $this->app[Kernel::class];
        $kernel->pushMiddleware($middleware);
    }
}