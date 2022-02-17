<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/11
 * Time: 13:40.
 */

namespace App\Service;

use App\Application\Admin\Model\Setting;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\EventDispatcher\EventDispatcherInterface;

abstract class AbstractSettingService
{
    public function getSettings(string $group = ''): array
    {
        return SettingService::getInstance()
            ->getSettings($group);
    }

    public function saveSetting(array $setting_data, string $group = ''): bool
    {
        return SettingService::getInstance()
            ->saveSetting($setting_data, $group);
    }
}