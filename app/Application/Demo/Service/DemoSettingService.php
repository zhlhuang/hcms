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
    public function getDemoSetting()
    {
        return $this->getSettings('demo');
    }

    public function setDemoSetting($setting)
    {
        return $this->saveSetting($setting, 'demo');
    }
}