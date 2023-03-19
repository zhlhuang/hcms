<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use App\Application\Admin\Service\AdminUserService;
use App\Exception\ErrorException;
use App\Model\AbstractAuthModel;
use Hyperf\Database\Model\Relations\HasOne;
use Hyperf\Database\Model\SoftDeletes;

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
class AdminUser extends AbstractAuthModel
{
    use SoftDeletes;

    protected string $guard_key = 'session';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'admin_user';
    protected string $primaryKey = 'admin_user_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $guarded = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected  array $casts = [
        'admin_user_id' => 'integer',
        'role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    protected array $hidden = ['deleted_at', 'password'];

    public function getLoginUserInfo(): self
    {
        /**
         * @var self $user
         */
        $user = $this->getLoginUser();

        return $user;
    }

    /**
     * 创建管理用户
     *
     * @param string $username
     * @param string $password
     * @param string $real_name
     * @param int    $role_id
     * @return bool
     * @throws ErrorException
     */
    function createAdminUser(string $username, string $password, string $real_name, int $role_id): bool
    {
        //检查是否存在同样的用户名
        $username_exist = AdminUser::where('username', $username)
                ->whereNotIn('admin_user_id', [$this->admin_user_id])
                ->count() > 0;
        if ($username_exist) {
            throw new ErrorException("用户名{$username}已经存在");
        }
        if ($password !== '' || !$this->admin_user_id) {
            $this->password = self::makePassword($username, $password);
        }
        $login_user_role_id = AdminUserService::getInstance()
            ->getAdminUserRoleId();
        if ($login_user_role_id > 0) {
            //检查选择的角色是否合法
            $allow_child_role_ids = AdminUserService::getInstance()
                ->getAdminUserChildRoleIds();
            if (!in_array($role_id, $allow_child_role_ids)) {
                throw new ErrorException('请选择正确的角色');
            }
        }
        $this->role_id = $role_id;
        $this->real_name = $real_name;
        $this->username = $username;

        return $this->save();
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

    public function role(): HasOne
    {
        return $this->hasOne(AdminRole::class, 'role_id', 'role_id');
    }
}