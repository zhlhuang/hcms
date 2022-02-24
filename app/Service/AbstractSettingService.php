<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/11
 * Time: 13:40.
 */

namespace App\Service;

abstract class AbstractSettingService
{
    /**
     * @param string $group
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getSettings(string $group = '', string $key = '', $default = '')
    {
        $settings = SettingService::getInstance()
            ->getSettings($group);
        if ($key !== '') {
            return $settings[$key] ?? $default;
        }

        return $settings;
    }

    public function saveSetting(array $setting_data, string $group = ''): bool
    {
        return SettingService::getInstance()
            ->saveSetting($setting_data, $group);
    }
}