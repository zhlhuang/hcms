<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 16:54
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Service\Upload;

use App\Application\Admin\Model\UploadFile;
use App\Application\Admin\Service\AdminSettingService;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Upload\UploadedFile;

abstract class AbstractUploadDriver
{
    protected UploadFile $upload_file;
    protected array $config;


    #[Inject]
    protected AdminSettingService $setting;

    public function __construct(public ?UploadedFile $file = null, string $file_type = 'image')
    {
        $this->config = $this->setting->getUploadSetting();
        $this->upload_file = new UploadFile();
        $this->upload_file->file_drive = $this->config['upload_drive'] ?? '';
        $this->upload_file->file_type = $file_type;
        if ($this->file instanceof UploadedFile) {
            $this->upload_file->file_name = $this->file->getClientFilename();
            $this->upload_file->file_ext = $this->file->getExtension();
            $this->upload_file->file_size = $this->file->getSize();
        }
    }


    /**
     * 获取文件访问路径
     *
     * @param $file_url
     * @return string
     */
    abstract function getObjectUrl($file_url): string;

    /**
     * 获取文件缩略图访问路径
     *
     * @param UploadFile $upload_file
     * @param            $file_thumb
     * @return string
     */
    abstract function getObjectThumb(UploadFile $upload_file, $file_thumb): string;

    /**
     * 保存图片
     *
     * @param array $data
     * @return UploadFile
     */
    public abstract function save(array $data = []): UploadFile;

    /**
     * 获取图片的缩略图 不同驱动可以回去不同文件缩略图
     *
     * @return string
     */
    abstract protected function getFileThumb(): string;

    /**
     * 上传文件校验
     *
     * @throws ErrorException
     */
    protected function uploadValid()
    {
        //获取允许上传的文件格式
        $upload_allow_ext = $this->config['upload_allow_ext'] ?? '';
        $upload_file_size = $this->config['upload_file_size'] ?? '';
        //设置为空不做校验
        if ($upload_allow_ext !== '') {
            //都转成小写，不区分大小写
            $upload_allow_ext_array = explode('|', strtolower($upload_allow_ext));
            //将文件格式转化成小写
            if (!in_array(strtolower($this->upload_file->file_ext), $upload_allow_ext_array)) {
                throw new ErrorException('不支持上传该文件');
            }
        }

        if ($upload_file_size > 0 && $this->upload_file->file_size > ($upload_file_size * 1024)) {
            throw new ErrorException("文件上传不能大于{$upload_file_size}KB");
        }
    }


    /**
     * @param int $user_id
     * @return $this
     */
    public function setUserId(int $user_id): self
    {
        $this->upload_file->upload_user_id = $user_id;

        return $this;
    }

    /**
     * @param string $user_type
     * @return $this
     */
    public function setUserType(string $user_type): self
    {
        $this->upload_file->upload_user_type = $user_type;

        return $this;
    }

    /**
     * @param int $group_id
     * @return $this
     */
    public function setGroupId(int $group_id): self
    {
        $this->upload_file->group_id = $group_id;

        return $this;
    }
}