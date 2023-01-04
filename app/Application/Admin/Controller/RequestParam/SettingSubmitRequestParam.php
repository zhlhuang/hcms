<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/4 11:01
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller\RequestParam;

use App\Annotation\RequestParam;
use App\Application\Admin\Model\Setting;
use App\Controller\RequestParam\BaseRequestParam;

/**
 * @RequestParam()
 */
class SettingSubmitRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'setting_key' => 'required',
        'setting_value' => 'required',
        'type' => 'required',
    ];
    protected array $message = [
        'setting_key.required' => '请输入配置的key',
        'setting_value.required' => '请输入配置的值',
        'type.required' => '请选择配置类型',
    ];
    private int $setting_id = 0;
    private string $setting_key = '';
    private string $setting_value = '';
    private string $setting_description = '';
    private string $setting_group = '';
    private string $type = Setting::TYPE_STRING;

    /**
     * @return int
     */
    public function getSettingId(): int
    {
        return $this->setting_id;
    }

    /**
     * @return string
     */
    public function getSettingKey(): string
    {
        return $this->setting_key;
    }

    /**
     * @return string
     */
    public function getSettingValue(): string
    {
        return $this->setting_value;
    }

    /**
     * @return string
     */
    public function getSettingDescription(): string
    {
        return $this->setting_description;
    }

    /**
     * @return string
     */
    public function getSettingGroup(): string
    {
        return $this->setting_group;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}