<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/13
 * Time: 16:01.
 */

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\UploadFile;
use App\Exception\ErrorException;
use Hyperf\HttpMessage\Upload\UploadedFile;

class UploadService
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
    }

    /**
     * 上传文件校验
     *
     * @throws ErrorException
     */
    private function uploadValid()
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
     * 获取图片的缩略图
     *
     * @return string
     */
    private function getFileThumb(): string
    {
        $file_type = $this->upload_file->file_type;
        if ($file_type === UploadFile::FILE_TYPE_IMAGE) {
            //图片缩略图
            return $this->upload_file->file_url;
        }
        if ($file_type === UploadFile::FILE_TYPE_VIDEO) {
            //视频文件缩略图
            return '/assets/img/upload/video.png';
        }
        if ($file_type === UploadFile::FILE_TYPE_DOC) {
            //文档文件，根据文档后缀决定缩略图
            $array = [
                'pdf' => 'pdf.png',
                'ppt' => 'ppt.png',
                'pptx' => 'ppt.png',
                'doc' => 'doc.png',
                'docx' => 'doc.png',
                'xls' => 'xls.png',
                'xlsx' => 'xls.png',
                'file' => 'file.png',
                'video' => 'video.png'
            ];

            return '/assets/img/upload/' . ($array[$this->upload_file->file_ext] ?? 'file.png');
        }

        return '';
    }

    /**
     * 获取存储的目录
     *
     * @return string
     */
    private function getPathDir(): string
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
     * 保存图片
     *
     * @return UploadFile
     * @throws ErrorException
     */
    public function save(): UploadFile
    {
        $this->uploadValid();
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