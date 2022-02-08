<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\Database\Model\SoftDeletes;
use Hyperf\DbConnection\Db;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int            $role_id
 * @property string         $role_name
 * @property string         $description
 * @property int            $parent_role_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string         $deleted_at
 */
class AdminRole extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'role_id';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'admin_role';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role_id', 'parent_role_id', 'role_name', 'description'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'role_id' => 'integer',
        'parent_role_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    static function saveRole($role_id = 0, $data = [], $access_list = []): self
    {
        Db::beginTransaction();
        $role = self::updateOrCreate(['role_id' => $role_id], $data);
        if ($role->role_id > 0) {
            //保存权限信息。
            $in_role_access_ids = [];
            foreach ($access_list as $access_id) {
                $access_uri = Access::where('access_id', $access_id)
                    ->value('uri');
                if ($access_uri) {
                    $role_access = AdminRoleAccess::firstOrCreate([
                        'role_id' => $role->role_id,
                        'access_id' => $access_id,
                        'access_uri' => $access_uri,
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

        return $role;
    }

    function accessList(): HasMany
    {
        return $this->hasMany(AdminRoleAccess::class, 'role_id', 'role_id');
    }

    function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_role_id', 'role_id');
    }
}