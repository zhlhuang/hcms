<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use App\Application\Admin\Service\AccessService;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int                                                      $role_id
 * @property string                                                   $role_name
 * @property string                                                   $description
 * @property int                                                      $parent_role_id
 * @property \Carbon\Carbon                                           $created_at
 * @property \Carbon\Carbon                                           $updated_at
 * @property string                                                   $deleted_at
 * @property-read \Hyperf\Database\Model\Collection|AdminRoleAccess[] $accessList
 * @property-read \Hyperf\Database\Model\Collection|self[]            $children
 */
class AdminRole extends Model
{
    use SoftDeletes;

    protected string $primaryKey = 'role_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'admin_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['role_id', 'parent_role_id', 'role_name', 'description'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected  array $casts = [
        'role_id' => 'integer',
        'parent_role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * 获取角色所有下级角色id
     *
     * @param $role_id
     * @return array
     */
    static function getChildRoleIds($role_id): array
    {
        $role_ids = self::where('parent_role_id', $role_id)
            ->pluck('role_id')
            ->toArray();
        if (empty($role_ids)) {
            return [];
        }
        $c_role_ids = [];
        foreach ($role_ids as $c_role_id) {
            $c_role_ids += self::getChildRoleIds($c_role_id);
        }

        return array_merge($role_ids, $c_role_ids);
    }

    /**
     * 保存角色信息
     *
     * @param int   $role_id
     * @param array $data
     * @param array $access_list
     * @return static
     */
    static function saveRole(int $role_id = 0, array $data = [], array $access_list = []): self
    {
        Db::beginTransaction();
        $role = self::updateOrCreate(['role_id' => $role_id], $data);
        if ($role->role_id > 0) {
            //保存权限信息。
            $in_role_access_ids = [];
            foreach ($access_list as $access_id) {
                $access_uri = Access::where('access_id', $access_id)
                    ->value('uri');
                //检测当前登录管理员是否拥有该权限，防止前端传了当前角色没授权的权限
                $check_access = AccessService::getInstance()
                    ->checkAccess($access_uri);
                if ($access_uri && $check_access) {
                    $role_access = AdminRoleAccess::firstOrCreate([
                        'role_id' => $role->role_id,
                        'access_id' => $access_id,
                        'access_uri' => $access_uri
                    ]);
                    $in_role_access_ids[] = $role_access->access_id;
                }
            }
            //删除没有提交的记录
            AdminRoleAccess::where('role_id', $role->role_id)
                ->whereNotIn('access_id', $in_role_access_ids)
                ->delete();
        }
        Db::commit();
        //清除角色权限
        AccessService::getInstance()
            ->flushRoleAccessListCache($role->role_id);

        return $role;
    }

    /**
     * 角色关联的权限（注意role_id=0系统管理员是没有关联权限的）
     *
     * @return HasMany
     */
    function accessList(): HasMany
    {
        return $this->hasMany(AdminRoleAccess::class, 'role_id', 'role_id');
    }

    /**
     * 下级角色
     *
     * @return HasMany
     */
    function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_role_id', 'role_id');
    }
}