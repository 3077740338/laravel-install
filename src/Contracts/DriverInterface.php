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
| 安装动作
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Contracts;

use Learn\Install\Support\Item;
interface DriverInterface
{
    /**
     * 安装动作
     *
     * @param  \Learn\Install\Support\Item  $item
     * @return bool
     */
    public function install(Item $item) : bool;
}