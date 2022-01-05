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
class Item
{
    /**
     * 编码类型
     *
     * @var array
     */
    public static $charset_type_list = [
        //
        'utf8mb4' => [
            //
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'version' => 5.6,
        ],
        'utf8' => [
            //
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'version' => 5.0,
        ],
    ];
    /**
     * 数据库连接uri
     *
     * @var string
     */
    public $url;
    /**
     * 数据库类型
     *
     * @var string
     */
    public $connection;
    /**
     * 数据库编码
     *
     * @var string
     */
    public $charset;
    /**
     * 数据库版本
     *
     * @var string
     */
    public $version;
    /**
     * 字段排序规则
     *
     * @var string
     */
    public $collation;
    /**
     * 数据表引擎
     *
     * @var string
     */
    public $engine;
    /**
     * 数据库地址
     *
     * @var string
     */
    public $host;
    /**
     * 数据库端口
     *
     * @var string
     */
    public $port;
    /**
     * 数据库名称
     *
     * @var string
     */
    public $database;
    /**
     * 数据库账号
     *
     * @var string
     */
    public $username;
    /**
     * 数据库密码
     *
     * @var string
     */
    public $password;
    /**
     * 数据表前缀
     *
     * @var string
     */
    public $prefix;
    /**
     * 后台的地址
     *
     * @var string
     */
    public $admin_url;
    /**
     * 管理员账号
     *
     * @var string
     */
    public $admin_username;
    /**
     * 管理员密码
     *
     * @var string
     */
    public $admin_password;
    public $cover;
    /**
     * Object Oriented
     * 
     * @param  array  $options
     */
    public function __construct($options)
    {
        foreach ([
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
            'cover',
        ] as $key) {
            if (!isset($options[$key])) {
                throw new \RuntimeException(sprintf('Key:%s does not exist.', $key));
            }
        }
        if (empty($options['DB_CHARSET'])) {
            throw new \RuntimeException('Database encoding cannot be empty.');
        }
        $this->registerOptions($options);
    }
    /**
     * 属性赋值
     *
     * @param  array  $options
     * @return void
     */
    protected function registerOptions($options)
    {
        foreach ($options as $key => $value) {
            $key = strtolower(str_replace('DB_', '', $key));
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        foreach (static::$charset_type_list[$options['DB_CHARSET']] as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        if (is_null($this->port)) {
            $this->url = sprintf('%s://%s:%s@%s', $this->connection, $this->username, $this->password, $this->host);
        } else {
            $this->url = sprintf('%s://%s:%s@%s:%s', $this->connection, $this->username, $this->password, $this->host, $this->port);
        }
        $this->url .= sprintf('?charset=%s&collation=%s', $this->charset, $this->collation);
    }
}