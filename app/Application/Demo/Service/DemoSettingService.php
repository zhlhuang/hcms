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
    public function getDemoSetting(string $key = '', $default = '')
    {
        $setting = $this->getSettings('log');
        if ($key !== '') {
            return $setting[$key] ?? $default;
        }

        return $setting;
    }

    public function setDemoSetting($setting): bool
    {
        return $this->saveSetting($setting, 'demo');
    }
}