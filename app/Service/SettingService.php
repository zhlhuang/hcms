<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/11
 * Time: 13:40.
 */

namespace App\Service;

use App\Application\Admin\Model\Setting;
use Hyperf\Database\Model\Model;
use Hyperf\DbConnection\Db;
use Hyperf\Utils\Context;

class SettingService
{

    protected $setting_list;
    protected $group_setting;

    protected function __construct()
    {
        $this->updateData();
    }

    /**
     * 初始化/更新 配置
     */
    private function updateData()
    {
        $this->setting_list = Setting::where([])
            ->select(['setting_value', 'setting_key', 'type', 'setting_group'])
            ->get()
            ->each(function (Model $model) {
                $model->append('format_value');
            })
            ->toArray();

        $group_setting = [];
        foreach ($this->setting_list as $setting) {
            $group_setting[$setting['setting_group']][$setting['setting_key']] = $setting['format_value'];
        }
        $this->group_setting = $group_setting;
    }

    static function getInstance(): self
    {
        return Context::getOrSet(self::class, function () {
            return new self();
        });
    }

    /**
     * 根据配置的分组获取配置
     *
     * @param string $group
     * @return array
     */
    public function getSettings(string $group = ''): array
    {
        if ($group !== '') {
            return $this->group_setting[$group] ?? [];
        }

        return $this->setting_list;
    }

    /**
     * 通过[key=>value]格式更新配置
     *
     * @param array  $setting_data
     * @param string $group
     * @return bool
     */
    public function saveSetting(array $setting_data, string $group = ''): bool
    {
        Db::beginTransaction();
        foreach ($setting_data as $key => $value) {
            $setting = Setting::where('setting_key', $key)
                ->first();
            if (!$setting) {
                if (!$group) {
                    $group = explode('_', $key)[0] ?? "";
                }
                $setting = Setting::create([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_group' => $group,
                    'setting_description' => '',
                    'type' => Setting::TYPE_STRING,
                ]);
            } else {
                $setting->setting_value = $value;
                $setting->save();
            }

            if (!$setting->setting_id) {
                Db::rollBack();

                return false;
            }
        }
        //更新数据
        $this->updateData();
        Db::commit();

        return true;
    }
}