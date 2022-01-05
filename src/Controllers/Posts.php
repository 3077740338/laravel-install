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
| 安装程序主操作界面
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Controllers;

use Illuminate\Support\Str;
use Learn\Facades\Input;
use Learn\Install\Support\Util;
use Learn\Install\Support\Item;
use Learn\Install\Contracts\DriverInterface;
class Posts extends BaseController
{
    /**
     * 安装动作
     *
     * @var \Learn\Install\Contracts\DriverInterface
     */
    protected $driver;
    /**
     * 处理用户请求
     * 
     * @return mixed
     */
    public function service()
    {
        $options = Input::only([
            //
            'DB_CONNECTION',
            'DB_CHARSET',
            'DB_ENGINE',
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_PREFIX',
            'admin_url',
            'admin_username',
            'admin_password',
            'type',
        ]);
        $options['cover'] = Input::boolean('cover');
        if (method_exists($this, $options['type'])) {
            return call_user_func([$this, $options['type']], $options);
        }
        return $this->error(sprintf('Not supported:%s', $options['type']));
    }
    /**
     * 环境检测
     * 
     * @return mixed
     */
    protected function environmentalDetection()
    {
        if ($this->need_php > \phpversion()) {
            return $this->error(sprintf('本系统要求PHP版本 >= %s，当前PHP版本为：%s，请到虚拟主机控制面板里切换PHP版本，或联系空间商协助切换。', $this->need_php, \phpversion()));
        }
        if (!function_exists('gd_info')) {
            return $this->error('当前未开启GD库，无法进行安装');
        }
        if (!is_writable(base_path())) {
            return $this->error(sprintf('目录不可写:%s', base_path()));
        }
        if (!is_writable(storage_path('framework'))) {
            return $this->error(sprintf('目录不可写:%s', storage_path('framework')));
        }
        foreach ([
            //
            'curl_init',
            'fsockopen',
            'file_get_contents',
            'mb_convert_encoding',
            'json_encode',
            'json_decode',
            'simplexml_load_string',
            'mb_substr',
            'mb_strlen',
        ] as $func) {
            if (!function_exists($func)) {
                return $this->error(sprintf('%s函数不存在', $func));
            }
        }
        if (!class_exists('mysqli')) {
            return $this->error('mysqli类不存在');
        }
        if (!class_exists('ZipArchive')) {
            return $this->error('\\ZipArchive类不存在');
        }
        if (!class_exists('PDO')) {
            return $this->error('当前未开启PDO，无法进行安装');
        }
        $this->app['cache']->set('INSTALL_STEP', 'INSTALL');
        return $this->success('OK', route('install.step3'));
    }
    /**
     * 初始配置
     *
     * @param array $options
     * @return mixed
     */
    protected function start($options)
    {
        $item = new Item($options);
        $ret = Util::databaseValidation($item);
        if ($ret['code'] !== 200) {
            return $this->error($ret['message']);
        }
        if (!in_array($item->connection, ['sqlite', 'mysql', 'pgsql', 'sqlsrv'], true)) {
            return $this->error(sprintf('Not supported:%s', $item->connection));
        }
        $envOptions = [];
        foreach ($options as $key => $value) {
            if (strpos($key, 'DB_') !== false) {
                $envOptions[$key] = $value;
            }
        }
        $envOptions['DB_COLLATION'] = $item->collation;
        Util::writeNewEnvironmentFileWith($this->app->environmentFilePath(), $envOptions);
        return $this->success('配置成功');
    }
    /**
     * 导入并安装数据
     *
     * @param array $options
     * @return mixed
     */
    protected function install($options)
    {
        try {
            $result = $this->driver->install(new Item($options));
        } catch (\Throwable $e) {
            return $this->error(sprintf('安装失败，信息：%s', $e->getMessage()));
        }
        return $result ? $this->app['cache']->set('INSTALL_STEP', 'END') && $this->success('安装成功', route('install.step5')) : $this->error('安装失败，请联系技术协助！');
    }
    public function driver(DriverInterface $driver)
    {
        $this->driver = $driver;
    }
}