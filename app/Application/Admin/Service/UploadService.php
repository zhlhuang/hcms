<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/13
 * Time: 16:01.
 */

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\UploadFile;
use App\Application\Admin\Service\upload\AbstractUploadDriver;
use App\Application\Admin\Service\upload\AliyunMirrorUploadDriver;
use App\Application\Admin\Service\upload\LocalUploadDriver;
use App\Application\Admin\Service\upload\TencentMirrorUploadDriver;
use App\Exception\ErrorException;
use Hyperf\HttpMessage\Upload\UploadedFile;

/**
 * @method AbstractUploadDriver setUserId(int $user_id);
 * @method AbstractUploadDriver setUserType(string $user_type)
 * @method AbstractUploadDriver setGroupId(int $group_id)
 * @method UploadFile save()
 */
class UploadService
{
    /**
     * @var AbstractUploadDriver
     */
    protected $upload_driver;

    protected $driver_list = [
        UploadFile::UPLOAD_DRIVE_LOCAL => LocalUploadDriver::class,
        UploadFile::UPLOAD_DRIVE_ALIYUN_MIRROR => AliyunMirrorUploadDriver::class,
        UploadFile::UPLOAD_DRIVE_TENCENT_MIRROR => TencentMirrorUploadDriver::class,
    ];

    public function __call($name, $arguments)
    {
        return call_user_func([$this->upload_driver, $name], ...$arguments);
    }

    public function __construct(UploadedFile $file, string $file_type = 'image')
    {
        $upload_driver = AdminSettingService::getUploadSetting('upload_drive', UploadFile::UPLOAD_DRIVE_LOCAL);

        if (empty($this->driver_list[$upload_driver])) {
            throw new ErrorException('找不到对应的上传驱动');
        }
        $this->upload_driver = new $this->driver_list[$upload_driver]($file, $file_type);
    }
}