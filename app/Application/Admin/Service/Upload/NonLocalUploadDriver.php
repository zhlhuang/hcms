<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 16:54
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Service\Upload;

interface NonLocalUploadDriver
{
    /**
     * 第三方直传所需资料
     *
     * @param string $acl
     * @return array
     */
    function getUploadForm(string $acl = 'default'): array;
}