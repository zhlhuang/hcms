<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;
use Hyperf\Utils\Codec\Json;

/**
 * @property int                           $setting_id
 * @property string                        $setting_key
 * @property string                        $setting_description
 * @property string                        $setting_value
 * @property string                        $setting_group
 * @property string                        $type
 * @property \Carbon\Carbon                $created_at
 * @property \Carbon\Carbon                $updated_at
 * @property-read array|float|mixed|string $format_value
 */
class Setting extends Model
{
    protected string $primaryKey = 'setting_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'setting';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['setting_key', 'setting_description', 'setting_value', 'setting_group', 'type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['setting_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    const TYPE_STRING = 'string';
    const TYPE_NUMBER = 'number';
    const TYPE_JSON = 'json';

    /**
     * 获取格式化后的配置
     *
     * @return mixed
     */
    function getFormatValueAttribute(): mixed
    {
        $type = $this->attributes['type'] ?? self::TYPE_STRING;
        $value = $this->attributes['setting_value'] ?? '';
        if ($type === self::TYPE_NUMBER) {
            return (float)$value;
        }
        if ($type === self::TYPE_JSON) {
            $res = Json::decode($value);
            if (is_array($res)) {
                return $res;
            }
        }

        return $value;
    }

    function setSettingValueAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['setting_value'] = Json::encode($value);
        }
        if (is_numeric($value)) {
            //数字配置写入也要转成字符串
            $this->attributes['setting_value'] = $value . '';
        }
    }
}