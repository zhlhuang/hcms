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

class LocalUploadDriver extends AbstractUploadDriver
{
    public function save(): UploadFile
    {
        return $this->saveLocal();
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
}