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
| 检测系统是否安装
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Facades\View;
use Learn\Install\Support\Util;
class CheckWhetherTheSystemIsInstalled
{
    /** @var Container */
    protected $app;
    /**
     * 验证中排除的URI
     *
     * @var array
     */
    protected $except = [
        //
        'install/install_protocols.html',
        'install/environmental_detection.html',
        'install/parameter_setting.html',
        'install/installation_is_complete.html',
        'install/service.html',
        'install/static/assets',
    ];
    public function __construct(Container $container)
    {
        $this->app = $container;
    }
    /**
     * 中间件执行入口
     * @access public
     * @param Request $request
     * @param Closure $next
     * @return Response
     */
    public function handle($request, Closure $next)
    {
        if ($this->isReading($request) || $this->runningUnitTests() || $this->inExceptArray($request) || Util::checkSystemIsInstalled()) {
            return optional($request, function ($request) use($next) {
                if ($this->inExceptArray($request)) {
                    View::replaceNamespace('install', collect(config('view.paths'))->map(function ($path) {
                        return \sprintf('%s/install', $path);
                    })->push(Util::withPath('resources/view'))->all());
                }
                return $next($request);
            });
        }
        return redirect()->route('install.step1');
    }
    /**
     * 确定HTTP请求
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return $request->isMethod('OPTIONS');
    }
    /**
     * 应用程序是否正在运行单元测试
     *
     * @return bool
     */
    protected function runningUnitTests()
    {
        return $this->app->runningInConsole() && $this->app->runningUnitTests();
    }
    /**
     * 确定请求是否具有应通过验证的URI
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }
            if ($request->fullUrlIs($except) || $request->is($except) || str::startsWith(trim($request->getBaseUrl() . $request->getPathInfo(), '/'), $except)) {
                return true;
            }
        }
        return false;
    }
}