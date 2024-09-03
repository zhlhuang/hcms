<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/4 11:01
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class SettingSiteRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'setting' => 'required',
    ];
    protected array $message = [
        'setting.required' => '请输入配置的key',
    ];
    private array $setting = [];

    public function getSetting(): array
    {
        return $this->setting;
    }
}