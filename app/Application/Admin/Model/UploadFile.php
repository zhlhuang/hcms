<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use App\Application\Admin\Service\UploadService;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $file_id
 * @property string         $file_drive
 * @property int            $group_id
 * @property string         $file_path
 * @property string         $file_name
 * @property string         $file_type
 * @property string         $file_ext
 * @property int            $file_size
 * @property int            $upload_user_id
 * @property string         $upload_user_type
 * @property string         $acl
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 * @property string         $file_thumb
 * @property string         $file_url
 */
class UploadFile extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'file_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'upload_file';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'file_id' => 'integer',
        'group_id' => 'integer',
        'file_size' => 'integer',
        'upload_user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    const FILE_TYPE_IMAGE = 'image';
    const FILE_TYPE_VIDEO = 'video';
    const FILE_TYPE_DOC = 'doc';
    const UPLOAD_DRIVE_LOCAL = 'local';
    const UPLOAD_DRIVE_ALIYUN_MIRROR = 'aliyun_mirror';
    const UPLOAD_DRIVE_TENCENT_MIRROR = 'tencent_mirror';
    const UPLOAD_DRIVE_QCLOUD = 'qcloud';

    const ACL_DEFAULT = 'default';
    const ACL_PUBLIC = 'public-read';

    const USER_TYPE_ADMIN = 'admin';

    static function getDriverList()
    {
        return [
            ['value' => self::UPLOAD_DRIVE_LOCAL, 'name' => '本地上传'],
            //            ['value' => self::UPLOAD_DRIVE_ALIYUN_MIRROR, 'name' => '阿里云OSS镜像'],
            //            ['value' => self::UPLOAD_DRIVE_TENCENT_MIRROR, 'name' => '腾讯云COS镜像'],
            ['value' => self::UPLOAD_DRIVE_QCLOUD, 'name' => '腾讯云COS'],
        ];
    }

    public function getFileUrlAttribute($value): string
    {
        try {
            if ($this->file_drive != self::UPLOAD_DRIVE_LOCAL && $this->acl === self::ACL_DEFAULT) {
                $s = new UploadService();

                return $s->getObjectUrl($value);
            }

            return $value;
        } catch (\Throwable $exception) {
            return $value;
        }
    }

    public function getFileThumbAttribute($value): string
    {
        try {
            if ($this->file_drive != self::UPLOAD_DRIVE_LOCAL && $this->acl === self::ACL_DEFAULT) {
                $s = new UploadService();

                return $s->getObjectThumb($this, $value);
            }

            return $value;
        } catch (\Throwable $exception) {
            return $value;
        }
    }

    /**
     * 获取未被修改过的值
     *
     * @param $key
     * @param $default
     * @return mixed|string
     */
    public function getOriginalValue($key, $default = '')
    {
        return $this->attributes[$key] ?? $default;
    }
}