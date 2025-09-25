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

    public function getSafeLoginCode()
    {
        return $this->getSettings('site', 'site_safe_login_code', '');
    }

    public function getSafeLogin(): bool
    {
        return intval($this->getSettings('site', 'site_safe_login', 0)) === 1;
    }

    /**
     * 获取上传配置
     *
     * @param string $key
     * @param mixed  $default
     * @return mixed
     */
    public function getUploadSetting(string $key = '', mixed $default = ''): mixed
    {
        return $this->getSettings('upload', $key, $default);
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
     * @return mixed
     */
    public function getSiteSetting(string $key = '', mixed $default = ''): mixed
    {
        return $this->getSettings('site', $key, $default);
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
     * @return mixed
     */
    public function getLogSetting(string $key = '', $default = '')
    {
        return $this->getSettings('log', $key, $default);
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