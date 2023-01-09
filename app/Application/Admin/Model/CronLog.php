<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $log_id
 * @property string         $task_class
 * @property string         $cron_name
 * @property string         $cron_rule
 * @property string         $cron_memo
 * @property int            $result
 * @property string         $result_msg
 * @property int            $execute_time
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CronLog extends Model
{
    protected $primaryKey = 'log_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cron_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['cron_memo', 'cron_name', 'cron_rule', 'task_class'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'log_id' => 'integer',
        'result' => 'integer',
        'execute_time' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const RESULT_SUCCESS = 1;
    const RESULT_FAILED = 0;
}