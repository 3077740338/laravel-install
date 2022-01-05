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
| 安装程序基类
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Controllers;

use Learn\Facades\Input;
use Learn\Controller;
use Learn\Install\Support\Util;
use Learn\Install\Support\Item;
abstract class BaseController extends Controller
{
    /**
     * php最低版本要求
     *
     * @var string
     */
    protected $need_php;
    /**
     * 初始化操作
     *
     * @return viod
     */
    protected function initialize()
    {
        parent::initialize();
        $this->assign('title', 'Thinker安装向导');
        $this->assign('logo', 'THINKERCMS');
        $this->assign('powered', 'Powered by Thinker');
        $this->assign('domain', Input::root());
        $this->assign('steps', [
            //
            '1' => '安装协议',
            '2' => '环境检测',
            '3' => '参数设置',
            '5' => '安装完成',
        ]);
        $this->assign('version', 'v' . $this->app->version());
        $this->assign('charset_type_list', Item::$charset_type_list);
        // 检测是否安装过程序
        if (Util::checkSystemIsInstalled()) {
            $this->setError('你已经安装过该系统，如果想重新安装，请先删除锁文件（install.lock），然后再安装。');
        }
        $this->getTheRequiredPHPVersion();
    }
    /**
     * 获取所需php版本
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function getTheRequiredPHPVersion()
    {
        if (!is_null($this->need_php)) {
            return $this->need_php;
        }
        $composer = json_decode(file_get_contents($this->app->basePath('composer.json')), true);
        if ($needs = data_get($composer, 'require.php')) {
            if (strpos($needs, '|')) {
                [$version] = explode('|', $needs);
            } else {
                $version = $needs;
            }
            return $this->need_php = trim($version, '~^');
        }
        throw new \RuntimeException('Unable to detect needs version.');
    }
    /**
     * 设置并抛出错误信息
     *
     * @param  string $message 错误信息
     *
     * @throws HttpResponseException
     */
    protected function setError($message)
    {
        return $this->fetch('error', compact('message'))->throwResponse();
    }
}