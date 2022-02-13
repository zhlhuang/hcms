<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $file_id
 * @property string         $file_drive
 * @property int            $group_id
 * @property string         $file_url
 * @property string         $file_path
 * @property string         $file_name
 * @property string         $file_type
 * @property string         $file_ext
 * @property int            $file_size
 * @property int            $upload_user_id
 * @property string         $upload_user_type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
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
}