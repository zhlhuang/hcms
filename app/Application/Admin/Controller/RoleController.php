<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/6
 * Time: 22:50.
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\RequestParam\RoleSubmitRequestParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\Access;
use App\Application\Admin\Model\AdminRole;
use App\Application\Admin\Model\AdminRoleAccess;
use App\Application\Admin\Service\AccessService;
use App\Application\Admin\Service\AdminUserService;
use App\Controller\AbstractController;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/role")
 */
class RoleController extends AbstractController
{

    /**
     * @Api()
     * @DeleteMapping(path="delete/{role_id}")
     */
    function delete(int $role_id)
    {
        $role = AdminRole::find($role_id);
        if (!$role) {
            return $this->returnErrorJson('找不到该记录');
        }

        //如果有下级菜单，不能删除
        if (AdminRole::where('role_id', $role->role_id)
                ->count() > 0) {
            return $this->returnErrorJson('该角色有下级角色，不能删除');
        }

        return $role->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }


    /**
     * @Api()
     * @RequestMapping(path="edit",methods={"POST","PUT"})
     */
    function submitEdit()
    {
        $request_param = new RoleSubmitRequestParam();
        $request_param->validatedThrowMessage();

        $role_id = $request_param->getRoleId();
        $access_list = $request_param->getAccessList();
        $parent_role_id = $request_param->getParentRoleId();
        if ($parent_role_id > 0 && $role_id === $parent_role_id) {
            return $this->returnErrorJson('父级角色不能是自己或自己的下级');
        }
        $role = AdminRole::saveRole($role_id, [
            'parent_role_id' => $parent_role_id,
            'role_name' => $request_param->getRoleName(),
            'description' => $request_param->getDescription(),
        ], $access_list);

        return $role->role_id > 0 ? $this->returnSuccessJson(compact('role')) : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="edit/{role_id}")
     */
    function editInfo(int $role_id = 0)
    {
        //获取下级角色
        $admin_role_id = AdminUserService::getInstance()
            ->getAdminUserRoleId();
        $role_list = AdminRole::where('parent_role_id', $admin_role_id)
            ->with(['children'])
            ->select()
            ->get();
        $parent_role_id = (int)$this->request->input('parent_role_id', 0);
        //获取该管理员信息
        $role = AdminRole::where('role_id', $role_id)
            ->first() ?: [];
        //获取所有父类权限
        $parent_access_ids = Access::distinct()
            ->pluck('parent_access_id');
        //获得只有叶子节点的权限
        $role_access_ids = AdminRoleAccess::where('role_id', $role_id)
            ->whereNotIn('access_id', $parent_access_ids)
            ->pluck('access_id');

        //获取管理的权限列表
        if ($parent_role_id == 0) {
            $parent_role_id = $role->parent_role_id ?? $admin_role_id;
        }

        $access_list = AccessService::getInstance()
            ->getAccessByRoleId($parent_role_id);

        return compact('role_list', 'role', 'access_list', 'role_access_ids', 'admin_role_id');
    }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        $role_id = (int)$this->request->input('role_id', 0);

        return ['title' => $role_id > 0 ? '编辑角色' : '新增角色'];
    }

    /**
     * 角色列表
     * @Api()
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $lists = AdminRole::where('parent_role_id', AdminUserService::getInstance()
            ->getAdminUserRoleId())
            ->with([
                'children' => function (Relation $relation) {
                    $relation->with(['children']);
                }
            ])
            ->select()
            ->get();

        return compact('lists');
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}