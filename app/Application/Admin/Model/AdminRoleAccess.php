<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $role_access_id
 * @property int            $role_id
 * @property int            $access_id
 * @property string         $access_uri
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class AdminRoleAccess extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_role_access';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id', 'access_id', 'access_uri'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_access_id' => 'integer',
        'role_id' => 'integer',
        'access_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
}