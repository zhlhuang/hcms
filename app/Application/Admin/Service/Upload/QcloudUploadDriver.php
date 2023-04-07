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
use Hyperf\HttpMessage\Upload\UploadedFile;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Utils\Codec\Json;
use Qcloud\Cos\Client;

class QcloudUploadDriver extends AbstractUploadDriver implements NonLocalUploadDriver
{
    private string $secret_id;
    private string $secret_key;
    private string $region;
    private string $bucket;
    private int $is_private;

    public function __construct(UploadedFile $file = null, string $file_type = 'image')
    {
        parent::__construct($file, $file_type);

        $this->secret_id = $this->config['qcloud_secret_id'] ?? "";
        $this->secret_key = $this->config['qcloud_secret_key'] ?? "";
        $this->region = $this->config['qcloud_region'] ?? "";
        $this->bucket = $this->config['qcloud_bucket'] ?? "";
        $this->is_private = intval($this->config['qcloud_is_private'] ?? 0);

    }

    private function getCosClient(): Client
    {
        $config = [
            'signHost' => false,
            'region' => $this->region,
            'schema' => 'https', //协议头部，默认为http
            'credentials' => ['secretId' => $this->secret_id, 'secretKey' => $this->secret_key],
        ];

        return new Client($config);
    }

    public function save(array $data = []): UploadFile
    {
        //保存文件信息
        $this->upload_file->file_name = $data['file_name'] ?? '';
        $this->upload_file->file_ext = strtolower($data['file_ext'] ?? '');
        $this->upload_file->file_size = $data['file_size'] ?? 0;
        $this->upload_file->file_url = $data['file_url'] ?? '';
        $this->upload_file->acl = $data['acl'] ?? 'default';
        $this->upload_file->file_thumb = $this->getFileThumb();
        $this->uploadValid();
        if (!$this->upload_file->save()) {
            throw new ErrorException('保存文件错误');
        }

        return $this->upload_file;
    }

    public function getObjectThumb(UploadFile $upload_file, $file_thumb): string
    {
        //如果是开启私有读，则需要构建临时访问地址
        if ($this->is_private === 0) {
            return $file_thumb;
        }

        $file_thumb = str_replace("?", '', $file_thumb);

        return str_replace([$upload_file->getOriginalValue('file_url', '')], $upload_file->file_url, $file_thumb);
    }

    public function getObjectUrl($file_url): string
    {
        //如果是开启私有读，则需要构建临时访问地址
        if ($this->is_private === 0) {
            return $file_url;
        }
        $cos = $this->getCosClient();
        $key = str_replace(['https://', 'http://', "{$this->bucket}.cos.{$this->region}.myqcloud.com/"], '', $file_url);
        try {
            return $cos->getObjectUrl($this->bucket, $key, '+10 minutes');
        } catch (\Throwable $exception) {
            return $file_url;
        }
    }

    /**
     * 获取COS上传需要的信息
     *
     * @param string $acl default 继承权限、 public-read 公共读
     * @return array
     */
    public function getUploadForm(string $acl = 'default'): array
    {
        //获取签名保护，不再主动设置跨域，需要到腾讯控制台配置
//        $this->putBucketCors(md5($this->secret_id . $this->secret_key . $this->region . $this->bucket));

        $expire_time = time() + 1800;
        $q_sign_time = time() . ";" . $expire_time;
        $policy = [
            'expiration' => date('Y-m-d\TH:i:s\Z', $expire_time - date('Z')),
            'conditions' => [
                ['q-sign-algorithm' => 'sha1'],
                ['bucket' => $this->bucket],
                ['q-ak' => $this->secret_id],
                ['q-sign-time' => $q_sign_time]
            ]
        ];
        $sign_key = hash_hmac('sha1', $q_sign_time, $this->secret_key, false);
        $string_to_sign = sha1(Json::encode($policy, 1));
        $signature = hash_hmac('sha1', $string_to_sign, $sign_key, false);

        $form_data = [
            'policy' => base64_encode(Json::encode($policy, 1)),
            'x-cos-acl' => $acl,
            'q-sign-algorithm' => 'sha1',
            'q-ak' => $this->secret_id,
            'q-key-time' => $q_sign_time,
            'q-signature' => $signature
        ];
        $post_url = sprintf("https://%s.cos.%s.myqcloud.com", $this->bucket, $this->region);

        return compact('post_url', 'form_data', 'acl');
    }

    /**
     * @deprecated
     * 设置存储桶的跨域请求，设置缓存防止每次都调用
     */
    #[Cacheable(prefix: "qcloud", ttl: 864000, listener: "qcloud-bucket-cors")]
    private function putBucketCors(string $cache_key = ''): bool
    {
        $cos = $this->getCosClient();
        try {
            $cos->PutBucketCors([
                'Bucket' => $this->bucket,
                'CORSRules' => [
                    [
                        'AllowedOrigins' => ['*'],
                        'AllowedMethods' => ['GET', 'PUT', 'HEAD', 'POST', 'DELETE'],
                        'AllowedHeaders' => ['*'],
                        'ExposeHeaders' => ['*']
                    ],
                ]
            ]);

            return true;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * 获取缩略图
     *
     * @return string
     */
    protected function getFileThumb(): string
    {
        $file_type = $this->upload_file->file_type;
        if ($file_type === UploadFile::FILE_TYPE_IMAGE) {
            //腾讯云图片处理成缩略图
            return $this->upload_file->getOriginalValue('file_url') . '?imageMogr2/gravity/center/crop/200x200/interlace/0';
        }
        if ($file_type === UploadFile::FILE_TYPE_VIDEO) {
            //腾讯云视频通过快照获取缩略图，注意需要开启媒体处理
            return $this->upload_file->getOriginalValue('file_url') . '?ci-process=snapshot&time=1&format=png&time=2&width=200&height=200';
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