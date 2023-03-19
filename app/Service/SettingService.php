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
use Hyperf\Context\Context;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class SettingService
{

    protected array $setting_list;
    protected array $group_setting;

    #[Inject]
    protected EventDispatcherInterface $dispatcher;

    private function __construct()
    {
    }

    static function getInstance(): self
    {
        return Context::getOrSet(self::class, function () {
            return new self();
        });
    }

    protected function getSettingList(): array
    {
        if (empty($this->setting_list)) {
            $this->setting_list = Setting::where([])
                ->select(['setting_value', 'setting_key', 'type', 'setting_group'])
                ->get()
                ->each(function (Model $model) {
                    $model->append('format_value');
                })
                ->toArray();
        }

        return $this->setting_list;
    }

    /**
     * @return mixed
     */
    protected function getGroupSetting()
    {
        if (empty($this->group_setting)) {
            $setting_list = $this->getSettingList();

            $group_setting = [];
            foreach ($setting_list as $setting) {
                $group_setting[$setting['setting_group']][$setting['setting_key']] = $setting['format_value'];
            }
            $this->group_setting = $group_setting;
        }

        return $this->group_setting;
    }

    /**
     * 根据配置的分组获取配置
     */
    #[Cacheable(prefix: "setting", ttl: 86400, listener: "setting-update")]
    public function getSettings(string $group = ''): array
    {
        if ($group !== '') {
            $group_setting = $this->getGroupSetting();

            return $group_setting[$group] ?? [];
        }

        return $this->getSettingList();
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
        $this->flushCache($group);
        Db::commit();

        return true;
    }

    /**
     * 清空指定分组的缓存
     *
     * @param string $group
     */
    public function flushCache(string $group = '')
    {
        //清空缓存
        $this->dispatcher->dispatch(new DeleteListenerEvent('setting-update', ['group' => $group]));
    }
}