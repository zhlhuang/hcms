<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/11
 * Time: 13:47.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Service\AbstractSettingService;

class AdminSettingService extends AbstractSettingService
{
    /**
     * 获取上传配置
     *
     * @param string $key
     * @param mixed  $default
     * @return array|mixed|string
     */
    public function getUploadSetting(string $key = '', $default = '')
    {
        $upload_setting = $this->getSettings('upload');
        if ($key !== '') {
            return $upload_setting[$key] ?? $default;
        }

        return $upload_setting;
    }

    /**
     * 保存上传配置
     *
     * @param array $setting
     * @return bool
     */
    public function setUploadSetting(array $setting): bool
    {
        return $this->saveSetting($setting, 'upload');
    }

    /**
     * 获取站点设置
     *
     * @param string $key
     * @param mixed  $default
     * @return array|mixed|string
     */
    public function getSiteSetting(string $key = '', $default = '')
    {
        $site_setting = $this->getSettings('site');
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
    public function setSiteSetting(array $setting): bool
    {
        return $this->saveSetting($setting, 'site');
    }

    /**
     * 获取日志配置
     *
     * @param string $key
     * @param mixed  $default
     * @return array|mixed|string
     */
    public function getLogSetting(string $key = '', $default = '')
    {
        $setting = $this->getSettings('log');
        if ($key !== '') {
            return $setting[$key] ?? $default;
        }

        return $setting;
    }

    /**
     * 保存日志配置
     *
     * @param array $setting
     * @return bool
     */
    public function setLogSetting(array $setting): bool
    {
        return $this->saveSetting($setting, 'log');
    }
}