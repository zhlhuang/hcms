<?php

declare (strict_types=1);

namespace App\Application\Admin\Model;

use App\Application\Admin\Service\AccessService;
use Hyperf\Database\Model\Relations\HasMany;
use Hyperf\DbConnection\Model\Model;
use Hyperf\ModelCache\Cacheable;

/**
 * @property int                                             $access_id
 * @property int                                             $parent_access_id
 * @property string                                          $access_name
 * @property string                                          $uri
 * @property string                                          $params
 * @property int                                             $sort
 * @property int                                             $is_menu
 * @property string                                          $menu_icon
 * @property \Carbon\Carbon                                  $created_at
 * @property \Carbon\Carbon                                  $updated_at
 * @property-read \Hyperf\Database\Model\Collection|Access[] $children
 */
class Access extends Model
{
    use Cacheable;

    protected $primaryKey = 'access_id';
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'access';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['parent_access_id', 'access_name', 'uri', 'params', 'sort', 'is_menu', 'menu_icon'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'access_id' => 'integer',
        'parent_access_id' => 'integer',
        'sort' => 'integer',
        'is_menu' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    const IS_MENU_YES = 1;
    const IS_MENU_NO = 0;

    public function children(): HasMany
    {
        //关联的数据也需要排序
        return $this->hasMany(Access::class, 'parent_access_id', 'access_id')
            ->orderBy('sort')
            ->orderBy('access_id');
    }

    public function saved()
    {
        //更新权限菜单和权限
        AccessService::getInstance()
            ->updateAccess();
    }

    public function deleted()
    {
        //更新权限菜单和权限
        AccessService::getInstance()
            ->updateAccess();
    }

}