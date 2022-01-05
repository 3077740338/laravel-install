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
*/
namespace Learn\Install\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
class Util
{
    /**
     * 检测系统是否安装
     * 
     * @return bool
     */
    public static function checkSystemIsInstalled()
    {
        return is_file(base_path('install.lock'));
    }
    /**
     * 处理安装文件
     * 
     * @return bool
     */
    public static function setupLockFile()
    {
        try {
            file_put_contents(base_path('install.lock'), date('Y-m-d H:i:s'));
        } catch (\Throwable $e) {
            return false;
        }
        return is_file(base_path('install.lock'));
    }
    /**
     * 获取当前目录子目录
     * 
     * @param  string $path
     * @return string
     */
    public static function withPath($path = '')
    {
        return _realpath(dirname(__DIR__) . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
    /**
     * 检查数据库是否存在
     *
     * @param  \Learn\Install\Support\Item  $item
     * @return mixed
     */
    public static function databaseValidation(Item $item)
    {
        if (($driver = DB::getConfig('driver')) !== 'mysql') {
            return static::compileMessage(404, sprintf('不支持的扩展，请使用控制台操作：%s', $driver));
        }
        $default = config('database.default');
        DB::purge($default);
        config()->set(sprintf('database.connections.%s.url', $default), $item->url);
        config()->set(sprintf('database.connections.%s.database', $default), '');
        try {
            if (is_null($version = DB::connection()->getPdo()->getAttribute(\PDO::ATTR_SERVER_VERSION))) {
                return static::compileMessage(404, '查询数据库版本失败');
            }
            $version = \str_replace('-log', '', $version);
            if ($version < $item->version) {
                return static::compileMessage(404, sprintf('数据库版本过低、需要>=%s、当前%s', $item->version, $version));
            }
            if (!empty(DB::select('SELECT * FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?', [$item->database]))) {
                if ($item->cover === false) {
                    return static::compileMessage(404, '数据库已存在，请选择覆盖安装或者修改数据库名');
                }
                return static::compileMessage(200, '数据库已存在，已选择覆盖安装');
            }
            return static::createDatabase($item->database);
        } catch (\Throwable $e) {
            return static::compileMessage(404, sprintf('数据库连接失败，请重新设定，错误信息：%s', $e->getMessage()));
        }
    }
    /**
     * 创建数据库
     * 
     * @param  string $database
     * @return array
     */
    public static function createDatabase($database)
    {
        try {
            if (Schema::createDatabase($database) === false) {
                return static::compileMessage(404, '数据库创建失败');
            }
            return static::compileMessage(200, '数据库创建成功');
        } catch (\Throwable $e) {
            return static::compileMessage(404, $e->getMessage());
        }
    }
    /**
     * 编译返回数据
     *
     * @param  int $code
     * @param  string $message
     * @return array
     */
    public static function compileMessage(int $code, string $message)
    {
        return ['code' => $code, 'message' => $message];
    }
    /**
     * 获取一个正则表达式模式，该模式将匹配$key键和任意值$value
     * @param  string  $key
     * @param  mixed   $value
     * @return string
     */
    public static function replacementPattern($key, $value)
    {
        $escaped = preg_quote('=' . $value, '/');
        return "/^{$key}{$escaped}/m";
    }
    /**
     * 编写一个新的环境文件
     *
     * @param  string  $path
     * @param  array  $envOptions
     * @return void
     */
    public static function writeNewEnvironmentFileWith($path, $envOptions)
    {
        if (is_file($backup = $path . '.backup')) {
            unlink($backup);
        }
        copy($path, $backup);
        $contents = file_get_contents($path);
        foreach ($envOptions as $key => $value) {
            $contents = preg_replace(static::replacementPattern($key, env($key)), sprintf('%s=%s', $key, $value), $contents);
        }
        file_put_contents($path, $contents);
    }
}