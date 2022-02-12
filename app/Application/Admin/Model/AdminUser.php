<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Model\Model;
use Qbhy\HyperfAuth\AuthAbility;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int            $admin_user_id
 * @property string         $username
 * @property string         $password
 * @property int            $role_id
 * @property string         $real_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 * @property-read string    $role_name
 * @property-read AdminRole $role
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
    protected $hidden = ['deleted_at'];

    /**
     * 获取所属角色名称
     *
     * @return string
     */
    public function getRoleNameAttribute(): string
    {
        if ($this->role_id === 0) {
            return "系统管理员";
        }

        return $this->role && $this->role->role_name ? $this->role->role_name : '';
    }

    /**
     * 生成离散登录密码
     *
     * @param string $username
     * @param string $password
     * @return string
     */
    static function makePassword(string $username, string $password): string
    {
        return md5($password . $username);
    }

    public function role(): HasOne
    {
        return $this->hasOne(AdminRole::class, 'role_id', 'role_id');
    }
}