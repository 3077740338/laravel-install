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
| 静态文件
|----------------------------------------------------------------------------
*/
declare (strict_types=1);
namespace Learn\Install\Controllers;

use Illuminate\Support\Arr;
use Learn\Install\Support\Util;
use Learn\Install\Support\CanPretendToBeAFile;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
class ShowStatic
{
    use CanPretendToBeAFile;
    /**
     * 获取文件类型信息
     * @access protected
     * @param string $Pathname
     * @return string
     */
    protected function getMime($Pathname) : string
    {
        return \finfo_file(\finfo_open(\FILEINFO_MIME_TYPE), $Pathname);
    }
    public function show($path = null)
    {
        $path = rtrim(trim(preg_replace('/\\?.*/', '', $path)), '/');
        $basePath = Util::withPath('resources/static');
        if (substr($basePath, -1) != \DIRECTORY_SEPARATOR) {
            $basePath .= \DIRECTORY_SEPARATOR;
        }
        $file = $this->setFile(_realpath($basePath . $path, null), false);
        $filename = $file->getPathname();
        $types = ['css' => 'text/css', 'js' => 'application/javascript', 'png' => 'image/png'];
        $ext = strtolower(Arr::last(explode('.', $filename)));
        if (array_key_exists($ext, $types)) {
            $mimeType = $types[$ext];
        } else {
            $mimeType = $this->getMime($filename);
        }
        return $this->pretendResponseIsFile($file, $mimeType);
    }
}