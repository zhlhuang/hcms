<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/13
 * Time: 16:01.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\UploadFile;
use App\Application\Admin\Service\Upload\AbstractUploadDriver;
use App\Application\Admin\Service\Upload\AliyunMirrorUploadDriver;
use App\Application\Admin\Service\Upload\LocalUploadDriver;
use App\Application\Admin\Service\Upload\QcloudUploadDriver;
use App\Application\Admin\Service\Upload\TencentMirrorUploadDriver;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;

/**
 * @method AbstractUploadDriver setUserId(int $user_id);
 * @method AbstractUploadDriver setUserType(string $user_type)
 * @method AbstractUploadDriver setGroupId(int $group_id)
 * @method UploadFile save(array $data = [])
 * @method array getUploadForm()
 * @method string getObjectUrl(string $file_url)
 */
class UploadService
{
    protected AbstractUploadDriver $upload_driver;

    /**
     * @Inject()
     */
    protected AdminSettingService $setting;

    protected $driver_list = [
        UploadFile::UPLOAD_DRIVE_LOCAL => LocalUploadDriver::class,
        UploadFile::UPLOAD_DRIVE_ALIYUN_MIRROR => AliyunMirrorUploadDriver::class,
        UploadFile::UPLOAD_DRIVE_TENCENT_MIRROR => TencentMirrorUploadDriver::class,
        UploadFile::UPLOAD_DRIVE_QCLOUD => QcloudUploadDriver::class,
    ];

    public function __call($name, $arguments)
    {
        return call_user_func([$this->upload_driver, $name], ...$arguments);
    }

    public function __construct(UploadedFile $file = null, string $file_type = 'image')
    {
        $upload_driver = $this->setting->getUploadSetting('upload_drive', UploadFile::UPLOAD_DRIVE_LOCAL);

        if (empty($this->driver_list[$upload_driver])) {
            throw new ErrorException('找不到对应的上传驱动');
        }
        $this->upload_driver = new $this->driver_list[$upload_driver]($file, $file_type);
    }
}