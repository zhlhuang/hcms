<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 */
class CronLog extends Model
{
    protected string $primaryKey = 'log_id';
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'cron_log';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['cron_memo', 'cron_name', 'cron_rule', 'task_class'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = [];

    const RESULT_SUCCESS = 1;
    const RESULT_FAILED = 0;
}