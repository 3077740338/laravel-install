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

use Learn\Install\Contracts\DriverInterface;
class Driver implements DriverInterface
{
    /**
     * 安装动作
     *
     * @param  \Learn\Install\Support\Item  $item
     * @return bool
     */
    public function install(Item $item) : bool
    {
        return true;
    }
}