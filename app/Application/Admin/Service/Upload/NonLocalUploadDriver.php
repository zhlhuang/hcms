<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 16:54
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Service\Upload;

use App\Application\Admin\Model\UploadFile;

interface NonLocalUploadDriver
{
    /**
     * 非本地存储，用户获取文件路径
     *
     * @param $file_url
     * @return string
     */
    function getObjectUrl($file_url): string;

    /**
     * 非本地存储，用户获取文件路径
     *
     * @param $file_url
     * @return string
     */
    function getObjectThumb(UploadFile $upload_file, $file_thumb): string;
}