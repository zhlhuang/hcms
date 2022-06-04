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
     * 第三方直传所需资料
     *
     * @param string $acl
     * @return array
     */
    function getUploadForm(string $acl = 'default'): array;

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
     * @param UploadFile $upload_file
     * @param            $file_thumb
     * @return string
     */
    function getObjectThumb(UploadFile $upload_file, $file_thumb): string;
}