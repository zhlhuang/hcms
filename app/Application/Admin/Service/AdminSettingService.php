<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/11
 * Time: 13:47.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Service\SettingService;

class AdminSettingService
{
    /**
     * 获取上传配置
     *
     * @param string $key
     * @param mixed  $default
     * @return array|mixed|string
     */
    public static function getUploadSetting(string $key = '', $default = '')
    {
        $site_setting = SettingService::getInstance()
            ->getSettings('upload');
        if ($key !== '') {
            return $site_setting[$key] ?? $default;
        }

        return $site_setting;
    }

    /**
     * 保存上传配置
     *
     * @param array $setting
     * @return bool
     */
    public static function setUploadSetting(array $setting): bool
    {
        return SettingService::getInstance()
            ->saveSetting($setting, 'upload');
    }

    /**
     * 获取站点设置
     *
     * @param string $key
     * @param mixed  $default
     * @return array|mixed|string
     */
    public static function getSiteSetting(string $key = '', $default = '')
    {
        $site_setting = SettingService::getInstance()
            ->getSettings('site');
        if ($key !== '') {
            return $site_setting[$key] ?? $default;
        }

        return $site_setting;
    }

    /**
     * 保存站点设置
     *
     * @param array $setting
     * @return bool
     */
    public static function setSiteSetting(array $setting): bool
    {
        return SettingService::getInstance()
            ->saveSetting($setting, 'site');
    }
}