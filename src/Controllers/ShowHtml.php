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
| 安装程序静态界面
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Controllers;

use Learn\Install\Support\Util;
class ShowHtml extends BaseController
{
    /**
     * 使用协议
     * 
     * @return mixed
     */
    public function useProtocol()
    {
        $this->app['cache']->set('INSTALL_STEP', 'START');
        return $this->fetch("step1");
    }
    /**
     * 环境检测
     * 
     * @return mixed
     */
    public function environmentalDetection()
    {
        if ($this->app['cache']->pull('INSTALL_STEP') == 'START') {
            return $this->fetch("step2");
        }
        return redirect()->route('install.step1');
    }
    /**
     * 参数设置
     * 
     * @return mixed
     */
    public function parameterSetting()
    {
        if ($this->app['cache']->pull('INSTALL_STEP') == 'INSTALL') {
            return $this->filter('__currentHost__', function ($content) {
                return \str_replace("{__currentHost__}", $this->request->root(), $content);
            })->fetch("step3");
        }
        return redirect()->route('install.step1');
    }
    /**
     * 安装完成
     * 
     * @return mixed
     */
    public function installationIsComplete()
    {
        if ($this->app['cache']->pull('INSTALL_STEP') == 'END') {
            Util::setupLockFile();
            return $this->fetch("step5");
        }
        return redirect()->route('install.step1');
    }
}