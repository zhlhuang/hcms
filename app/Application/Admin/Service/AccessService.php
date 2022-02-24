<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/7
 * Time: 18:56.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\Access;
use App\Application\Admin\Model\AdminRoleAccess;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Context\Context;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Hyperf\Cache\Listener\DeleteListenerEvent;
use Psr\EventDispatcher\EventDispatcherInterface;


class AccessService
{

    protected array $all_access = [];
    protected array $menu_list = [];

    /**
     * @Inject()
     */
    protected EventDispatcherInterface $dispatcher;

    /**
     * 不需要校验的权限集合
     *
     * @var string[]
     */
    protected $not_auth = [];

    private function __construct()
    {
        $this->not_auth = config('access.not_auth');
    }

    static function getInstance(): self
    {
        return Context::getOrSet(self::class, function () {
            return new self();
        });
    }

    /**
     * 获取所有菜单
     *
     * @return array
     */
    private function getAllAccess(): array
    {
        if (empty($this->all_access)) {
            $this->all_access = $this->getAllAccessTree();
        }

        return $this->all_access;
    }

    /**
     * @Cacheable(prefix="access",ttl=86400,listener="access-update")
     */
    private function getAllAccessTree(int $parent_access_id = 0): array
    {
        return Access::where('parent_access_id', $parent_access_id)
            ->with([
                'children' => function (Relation $relation) {
                    $relation->with(['children']);
                }
            ])
            ->orderBy('sort')
            ->orderBy('access_id')
            ->select([
                'menu_icon',
                'access_name',
                'parent_access_id',
                'uri',
                'sort',
                'params',
                'access_id',
                'is_menu'
            ])
            ->get()
            ->toArray();
    }

    /**
     * 根据权限获取菜单
     *
     * @return array
     */
    private function getMenuList(): array
    {
        if (empty($this->menu_list)) {
            $this->menu_list = $this->makeMenuList($this->getAllAccess());
        }

        return $this->menu_list;
    }

    /**
     * 制作菜单
     *
     * @param array $access_list
     * @return array
     */
    private function makeMenuList(array $access_list = []): array
    {
        if (empty($access_list)) {
            return [];
        }
        $menu_list = [];
        foreach ($access_list as $item) {
            if ($item['is_menu'] === Access::IS_MENU_YES) {
                if (!empty($item['children'])) {
                    $item['children'] = $this->makeMenuList($item['children']);
                }
                $item['url'] = url($item['uri'], $item['params']);
                $item['menu_id'] = md5($item['url'] . $item['access_id']);
                $menu_list[] = $item;
            }
        }

        return $menu_list;
    }

    /**
     * 根据角色id获取菜单列表
     *
     * @param int $role_id
     * @return array
     */
    public function getMenuByRoleId(int $role_id = 0): array
    {
        if ($role_id === 0) {
            return $this->getMenuList();
        } else {
            $role_access_list = $this->getRoleAccessList($role_id);

            return $this->filterRoleAccess($this->getMenuList(), $role_access_list);
        }
    }

    /**
     * 获取角色权限记录  access_id => uri
     * @Cacheable(prefix="role_access",ttl=86400,listener="role-access-update")
     */
    private function getRoleAccessList(int $role_id): array
    {
        $role_access_list = array_merge($this->not_auth, AdminRoleAccess::where('role_id', $role_id)
            ->pluck('access_uri')
            ->toArray());
        sort($role_access_list);

        return $role_access_list;
    }

    /**
     * 清除角色权限缓存，在编辑角色权限的时候就需要清除
     */
    public function flushRoleAccessListCache($role_id)
    {
        //清空缓存
        $this->dispatcher->dispatch(new DeleteListenerEvent('role-access-update', ['role_id' => $role_id]));
    }

    /**
     * 筛选角色的权限
     *
     * @param $access_list
     * @param $role_access_list
     * @return array
     */
    private function filterRoleAccess($access_list, $role_access_list): array
    {
        $filter_access_list = [];
        foreach ($access_list as $value) {
            if (in_array($value['uri'], $role_access_list)) {
                if (!empty($value['children'])) {
                    $value['children'] = $this->filterRoleAccess($value['children'], $role_access_list);
                }
                $filter_access_list[] = $value;
            }
        }

        return $filter_access_list;
    }

    /**
     * 通过角色id获取权限列表
     *
     * @param int $role_id
     * @return array
     */
    public function getAccessByRoleId(int $role_id = 0): array
    {
        if ($role_id === 0) {
            return $this->getAllAccess();
        } else {
            $role_access_list = $this->getRoleAccessList($role_id);
            var_dump($role_access_list);

            return $this->filterRoleAccess($this->getAllAccess(), $role_access_list);
        }
    }

    /**
     * 根据path校验当前管理是否有权限
     *
     * @param string $path
     * @return bool
     */
    public function checkAccess(string $path): bool
    {
        $role_id = AdminUserService::getInstance()
            ->getAdminUserRoleId();
        if ($role_id === 0) {
            return true;
        }
        $role_access_list = $this->getRoleAccessList($role_id);
        //TODO 这里返回的权限列表已经是排序过的，下面的权限匹配可以考虑使用二分查找
        foreach ($role_access_list as $item) {
            if (Str::is($item, $path) || Str::is($item . '/*', $path)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 更新权限列表
     */
    public function updateAccess()
    {
        //清空缓存
        $this->dispatcher->dispatch(new DeleteListenerEvent('access-update', ['parent_access_id' => 0]));
    }
}