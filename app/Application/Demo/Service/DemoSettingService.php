<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/17 14:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Demo\Service;

use App\Service\AbstractSettingService;

class DemoSettingService extends AbstractSettingService
{

    /**
     * 自定义获取配置方法
     *
     * @return string
     */
    public function getString(): string
    {
        return $this->getDemoSetting('demo_string', '') . "";
    }

    public function getDemoSetting(string $key = '', $default = '')
    {
        return $this->getSettings('demo', $key, $default);
    }

    public function setDemoSetting($setting): bool
    {
        return $this->saveSetting($setting, 'demo');
    }
}