<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $group_id
 * @property string         $group_name
 * @property string         $file_type
 * @property \Carbon\Carbon $created_at
 * @property int            $upload_user_id
 * @property string         $upload_user_type
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class UploadFileGroup extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'group_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'upload_file_group';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['group_name', 'file_type', 'upload_user_id', 'upload_user_type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [
        'group_id' => 'integer',
        'created_at' => 'datetime',
        'upload_user_id' => 'integer',
        'updated_at' => 'datetime'
    ];

}