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
use Hyperf\HttpMessage\Upload\UploadedFile;

abstract class AbstractUploadDriver
{
    protected $file;
    protected $upload_file;
    protected $config;


    public function __construct(UploadedFile $file, string $file_type = 'image')
    {
        $this->file = $file;
        $this->config = AdminSettingService::getUploadSetting();
        $this->upload_file = new UploadFile();
        $this->upload_file->file_drive = $this->config['upload_drive'] ?? '';
        $this->upload_file->file_name = $this->file->getClientFilename();
        $this->upload_file->file_type = $file_type;
        $this->upload_file->file_ext = $this->file->getExtension();
        $this->upload_file->file_size = ceil($this->file->getSize() / 1024);//转化成KB单位
        //检查上传文件是否合法
        $this->uploadValid();
    }

    /**
     * 保存图片
     *
     * @return UploadFile
     * @throws ErrorException
     */
    public abstract function save(): UploadFile;

    /**
     * 获取图片的缩略图 不同驱动可以回去不同文件缩略图
     *
     * @return string
     */
    abstract protected function getFileThumb(): string;

    /**
     * 将文件保存本地，一般对于云服务的对象存储，采用镜像服务就可以存储在本地
     *
     * @return UploadFile
     * @throws \Exception
     */
    protected function saveLocal(): UploadFile
    {
        $upload_file_dir = $this->getPathDir();
        $dir_path = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $upload_file_dir . DIRECTORY_SEPARATOR;
        $file_name = time() . '.' . $this->file->getExtension();
        if (!is_dir($dir_path)) {
            mkdir($dir_path, 0700, true);
        }
        $file_path = $dir_path . $file_name;
        $file_url = ($this->config['upload_domain'] ?? "/") . $upload_file_dir . DIRECTORY_SEPARATOR . $file_name;
        $this->file->moveTo($file_path);

        $this->upload_file->file_url = $file_url;
        $this->upload_file->file_thumb = $this->getFileThumb();
        $this->upload_file->file_path = $file_path;
        if (!$this->upload_file->save()) {
            throw new \Exception('保存文件上传信息失败');
        }

        return $this->upload_file;
    }

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
            $upload_allow_ext_array = explode('|', $upload_allow_ext);
            if (!in_array($this->file->getExtension(), $upload_allow_ext_array)) {
                throw new ErrorException('不支持上传该文件');
            }
        }

        if ($upload_file_size > 0 && $this->upload_file->file_size > $upload_file_size) {
            throw new ErrorException("文件上传不能大于{$upload_file_size}KB");
        }
    }

    /**
     * 获取存储的目录
     *
     * @return string
     */
    protected function getPathDir(): string
    {
        $path_dir = '';
        $upload_file_dir = $this->config['upload_file_dir'] ?? '';
        if ($upload_file_dir != '/' && $upload_file_dir !== '') {
            $path_dir .= $upload_file_dir . DIRECTORY_SEPARATOR;
        }
        $path_dir .= $this->upload_file->file_type . DIRECTORY_SEPARATOR . date('Ym');

        return $path_dir;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): self
    {
        $this->upload_file->upload_user_id = $user_id;

        return $this;
    }

    /**
     * @param string $user_type
     */
    public function setUserType(string $user_type): self
    {
        $this->upload_file->upload_user_type = $user_type;

        return $this;
    }

    /**
     * @param int $group_id
     */
    public function setGroupId(int $group_id): self
    {
        $this->upload_file->group_id = $group_id;

        return $this;
    }
}