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

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\HttpException;
trait CanPretendToBeAFile
{
    /**
     * 虚拟响应文件
     *
     * @param \SplFileInfo|string $file 
     * @param string $mimeType
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function pretendResponseIsFile($file, $mimeType = 'application/javascript')
    {
        $file = $this->setFile($file);
        return (new BinaryFileResponse($file, 200, [
            'Content-Type' => sprintf('%s; charset=utf-8', $mimeType),
        ], true, null, false, true))->setMaxAge(31536000)->setExpires(\DateTime::createFromFormat('U', 1671804574));
    }
    /**
     * 虚拟响应内容
     *
     * @param string $data 
     * @param string $mimeType
     * @return \Illuminate\Http\Response
     */
    protected function pretendResponseIsData($data, $mimeType = 'application/javascript')
    {
        return (new Response($data, 200, [
            'Content-Type' => sprintf('%s; charset=utf-8', $mimeType),
        ]))->setMaxAge(31536000)->setExpires(\DateTime::createFromFormat('U', 1671804574));
    }
    /**
     * 将文件设置为流
     *
     * @param \SplFileInfo|string $file 
     * @return \Symfony\Component\HttpFoundation\File\File
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    protected function setFile($file)
    {
        if (!$file instanceof File) {
            if ($file instanceof \SplFileInfo) {
                $file = new File($file->getPathname(), false);
            } else {
                $file = new File((string) $file, false);
            }
        }
        if (!$file->isReadable()) {
            throw new HttpException(404, 'File must be readable.');
        }
        return $file;
    }
}