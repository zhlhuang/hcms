<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;

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
     * 获取格式化后的配置
     *
     * @return array|float|mixed|string
     */
    function getFormatValueAttribute()
    {
        $type = $this->type ?: self::TYPE_STRING;
        $value = $this->setting_value;
        if ($type === self::TYPE_NUMBER) {
            return (float)$value;
        }
        if ($type === self::TYPE_JSON) {
            $res = json_decode($value, true);
            if (is_array($res)) {
                return $res;
            }
        }

        return $value;
    }
}