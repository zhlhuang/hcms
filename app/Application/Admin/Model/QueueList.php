<?php

declare (strict_types=1);
namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;
/**
 * @property int $queue_id 
 * @property string $class_name 
 * @property string $method 
 * @property string $params 
 * @property string $params_md5 
 * @property int $status 
 * @property string $error_msg 
 * @property string $error_data 
 * @property int $process_time 
 * @property int $process_count 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 */
class QueueList extends Model
{
    protected string $primaryKey = 'queue_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'queue_list';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['class_name', 'method', 'params', 'params_md5', 'status', 'error_msg', 'error_data', 'process_time', 'process_count'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected  array $casts = ['queue_id' => 'integer', 'status' => 'integer', 'process_time' => 'integer', 'process_count' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
    const STATUS_PENDING = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 2;
}