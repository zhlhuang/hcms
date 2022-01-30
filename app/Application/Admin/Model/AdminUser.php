<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;
use Qbhy\HyperfAuth\AuthAbility;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int            $admin_user_id
 * @property string         $username
 * @property string         $password
 * @property int            $role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class AdminUser extends Model implements Authenticatable
{
    use AuthAbility, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_user';
    protected $primaryKey = 'admin_user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'admin_user_id' => 'integer',
        'role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

}