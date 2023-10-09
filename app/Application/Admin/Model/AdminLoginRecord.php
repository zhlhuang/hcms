<?php

declare(strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $id
 * @property string         $username
 * @property int            $login_result
 * @property string         $result_msg
 * @property string         $ip
 * @property string         $user_agent
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdminLoginRecord extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'admin_login_record';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = [];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = [
        'id' => 'integer',
        'login_result' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    const LOGIN_RESULT_SUCCESS = 1;
    const LOGIN_RESULT_FAIL = 0;
}
