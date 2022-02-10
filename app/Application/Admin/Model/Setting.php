<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $setting_id
 * @property string         $setting_key
 * @property string         $setting_description
 * @property string         $setting_value
 * @property string         $setting_group
 * @property string         $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Setting extends Model
{

    protected $primaryKey = 'setting_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['setting_key', 'setting_description', 'setting_value', 'setting_group', 'type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['setting_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];


    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_JSON = 'json';

    /**
     * 获取配置列表
     *
     * @param string $group
     * @return array
     */
    static function getSettings(string $group = ''): array
    {
        $where = [];
        if ($group !== '') {
            $where[] = ['setting_group', '=', $group];
        }

        return self::where($where)
            ->pluck('setting_value', 'setting_key')
            ->toArray() ?: [];
    }

    /**
     * 通过key=>value 更新配置
     *
     * @param array  $setting_data
     * @param string $group
     * @return bool
     */
    static function saveSetting(array $setting_data, string $group = ''): bool
    {
        Db::beginTransaction();
        foreach ($setting_data as $key => $value) {
            $setting = self::where('setting_key', $key)
                ->first();
            if (!$setting) {
                if (!$group) {
                    $group = explode('_', $key)[0] ?? "";
                }
                $setting = self::create([
                    'setting_key' => $key,
                    'setting_value' => $value,
                    'setting_group' => $group,
                    'setting_description' => '',
                    'type' => self::TYPE_STRING,
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
        Db::commit();

        return true;
    }
}