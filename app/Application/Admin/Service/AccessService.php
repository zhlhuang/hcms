<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/7
 * Time: 18:56.
 */

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\Access;
use App\Application\Admin\Model\AdminRoleAccess;
use Hyperf\Utils\Context;

class AccessService
{

    protected $all_access = [];
    protected $menu_list = [];

    private function __construct()
    {
        $this->all_access = $this->getAccessList();
        $this->menu_list = $this->getMenuList($this->all_access);
    }

    private function getMenuList($access_list)
    {
        $menu_list = [];
        foreach ($access_list as $item) {
            if ($item['is_menu'] === Access::IS_MENU_YES) {
                if (!empty($item['children'])) {
                    $item['children'] = $this->getMenuList($item['children']);
                }
                $item['url'] = url($item['uri'], $item['params']);
                $item['menu_id'] = md5($item['url'] . $item['access_id']);
                $menu_list[] = $item;
            }
        }

        return $menu_list;
    }

    private function getAccessList($parent_access_id = 0)
    {
        $access_list = Access::where('parent_access_id', $parent_access_id)
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
        if (empty($access_list)) {
            return [];
        }
        foreach ($access_list as &$item) {
            $item['children'] = $this->getAccessList($item['access_id']);
            $item['url'] = url($item['uri'], $item['params']);
            $item['menu_id'] = md5($item['url'] . $item['access_id']);
        }

        return $access_list;
    }

    static function getInstance(): self
    {
        return Context::getOrSet(self::class, function () {
            return new self();
        });
    }

    public function getMenuByRoleId($role_id = 0)
    {
        if ($role_id === 0) {
            return $this->menu_list;
        } else {
            $role_access_list = $this->getRoleAccessList($role_id);
            return $this->filterRoleAccess($this->menu_list, $role_access_list);
        }
    }


    private function getRoleAccessList($role_id)
    {
        //获取角色权限列表
        return AdminRoleAccess::where('role_id', $role_id)
            ->pluck('access_uri', 'access_id')
            ->toArray();
    }


    private function filterRoleAccess($access_list, $role_access_list)
    {
        $filter_access_list = [];
        foreach ($access_list as $value) {
            if (!empty($role_access_list[$value['access_id']])) {
                if (!empty($value['children'])) {
                    $value['children'] = $this->filterRoleAccess($value['children'], $role_access_list);
                }
                $filter_access_list[] = $value;
            }
        }

        return $filter_access_list;
    }

    public function getAccessByRoleId($role_id = 0)
    {
        if ($role_id === 0) {
            return $this->all_access;
        } else {
            $role_access_list = $this->getRoleAccessList($role_id);
            return $this->filterRoleAccess($this->all_access, $role_access_list);
        }
    }

    public function updateAccess()
    {
        $this->all_access = $this->getAccessList();
        $this->menu_list = $this->getMenuList($this->all_access);
    }
}