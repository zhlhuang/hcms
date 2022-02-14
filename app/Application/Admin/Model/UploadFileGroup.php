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
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class UploadFileGroup extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'group_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'upload_file_group';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['group_name', 'file_type'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['group_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

}