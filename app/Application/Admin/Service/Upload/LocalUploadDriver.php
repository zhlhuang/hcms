<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 16:54
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Service\Upload;

use App\Application\Admin\Model\UploadFile;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpMessage\Upload\UploadedFile;

class LocalUploadDriver extends AbstractUploadDriver
{
    #[Inject]
    protected RequestInterface $request;

    public function save(array $data = []): UploadFile
    {
        //检查上传文件是否合法
        $this->uploadValid();
        if (!($this->file instanceof UploadedFile)) {
            throw new ErrorException('未找到上传文件');
        }
        $upload_file_dir = $this->getPathDir();
        $dir_path = BASE_PATH . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . $upload_file_dir . DIRECTORY_SEPARATOR;
        $file_name = md5(time() . rand(1000, 9999)) . '.' . $this->file->getExtension();
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

    protected function getFileThumb(): string
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

    function getObjectUrl($file_url): string
    {
        if (strpos($file_url, 'http') === 0) {
            return $file_url;
        } else {
            //不是http开头，拼接访问路径
            return $this->getDomain() . $file_url;
        }
    }

    function getObjectThumb(UploadFile $upload_file, $file_thumb): string
    {
        if (strpos($file_thumb, 'http') === 0) {
            return $file_thumb;
        } else {
            //不是http开头，拼接访问路径
            return $this->getDomain() . $file_thumb;
        }
    }

    /**
     * 获取域名
     *
     * @return string
     */
    private function getDomain(): string
    {
        $domain = $this->request->getUri()
                ->getScheme() . "://" . $this->request->getUri()
                ->getHost();

        $port = $this->request->getUri()
            ->getPort();
        if ($port != '' && ($port != '80' || $port != '443')) {
            $domain .= ':' . $this->request->getUri()
                    ->getPort();
        }

        return $domain;
    }
}