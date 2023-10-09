<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/7/2 17:24
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Service;

use Hyperf\Utils\Codec\Json;

class ApiService
{
    protected bool $encode_data = false;
    protected string $api_key = '';

    public function __construct()
    {
        $this->encode_data = boolval(\Hyperf\Config\config('api.encode_data'));
        $api_key = \Hyperf\Config\config('api.key', '');
        //如果配置中没有key，则默认使用version的md5
        $this->api_key = $api_key ?: substr(md5(\Hyperf\Config\config('version.version')), 8, 16);
    }

    /**
     * @return bool
     */
    public function getEncodeData(): bool
    {
        return $this->encode_data;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->api_key;
    }

    /**
     * 加密数据
     *
     * @param array $data
     * @return array
     */
    public function encryptData(array $data = []): array
    {
        if ($this->getEncodeData()) {
            $data_str = Json::encode($data);
            $key = $this->getApiKey();
            $encode = base64_encode(openssl_encrypt($data_str, "AES-128-ECB", $key, 1));

            return [
                'data' => $encode,
                'is_encrypt' => true //告诉前端，数据是否加密
            ];
        } else {
            return $data;
        }
    }
}