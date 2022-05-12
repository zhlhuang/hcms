<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/6
 * Time: 22:50.
 */
declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\AdminRole;
use App\Application\Admin\Model\AdminUser;
use App\Application\Admin\Service\AdminUserService;
use App\Controller\AbstractController;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/user")
 */
class UserController extends AbstractController
{
    /**
     * @PostMapping(path="delete")
     */
    function delete()
    {
        $admin_user_id = (int)$this->request->input('admin_user_id', 0);
        $admin_user = AdminUser::where('admin_user_id', $admin_user_id)
            ->first();
        if (!$admin_user) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $admin_user->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }


    /**
     * @PostMapping(path="edit")
     */
    function submitEdit()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'role_id' => 'required',
            'real_name' => 'required',
            'username' => 'required',
        ], [
            'role_id.required' => '请选择所属的角色',
            'real_name.required' => '请输入管理员姓名',
            'username.required' => '请输入登录用户名',
        ]);
        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }
        /**
         * @var AdminUser
         */
        $admin_user = AdminUser::firstOrNew([
            'admin_user_id' => $this->request->post('admin_user_id', 0)
        ]);
        $password = $this->request->post('password', '');
        $username = $this->request->post('username', '');
        $real_name = $this->request->post('real_name', '');
        if ($admin_user->admin_user_id === 0 && $password === '') {
            return $this->returnErrorJson('请输入密码');
        }
        if ($password !== '' || !$admin_user->admin_user_id) {
            $admin_user->password = AdminUser::makePassword($username, $password);
        }
        $role_id = (int)$this->request->post('role_id', 0);
        $res = $admin_user->createAdminUser($username, $password, $real_name, $role_id);

        return $res ? $this->returnSuccessJson(compact('admin_user')) : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="edit/info")
     */
    function editInfo()
    {
        //获取下级角色
        $admin_role_id = AdminUserService::getInstance()
            ->getAdminUserRoleId();
        $role_list = AdminRole::where('parent_role_id', $admin_role_id)
            ->with([
                'children' => function (Relation $relation) {
                    $relation->with('children');
                }
            ])
            ->select()
            ->get();
        $admin_user_id = (int)$this->request->input('admin_user_id', 0);

        $admin_user = AdminUser::where('admin_user_id', $admin_user_id)
            ->select(['admin_user_id', 'role_id', 'real_name', 'username'])
            ->first() ?: [];

        return $this->returnSuccessJson(compact('role_list', 'admin_user'));
    }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        $admin_user_id = (int)$this->request->input('admin_user_id', 0);

        return ['title' => $admin_user_id > 0 ? '编辑管理员' : '新增管理员'];
    }

    /**
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $where = [];
        $role_where = [];
        $real_name = $this->request->input('real_name', '');
        $username = $this->request->input('username', '');
        $role_id = (int)$this->request->input('role_id', 0);
        //当前管理员id
        $admin_role_id = AdminUserService::getInstance()
            ->getAdminUserRoleId();

        if ($real_name !== '') {
            $where[] = ['real_name', 'like', "%{$real_name}%"];
        }
        if ($username !== '') {
            $where[] = ['username', 'like', "%{$username}%"];
        }
        if ($role_id !== 0) {
            $where[] = ['role_id', '=', $role_id];
        } elseif ($admin_role_id > 0) {
            //不是系统管理员，则只能获取下级角色的管理员
            $admin_role_child_role_ids = AdminRole::getChildRoleIds($admin_role_id);
            $where[] = $role_where[] = [
                function ($q) use ($admin_role_child_role_ids) {
                    $q->whereIn('role_id', $admin_role_child_role_ids);
                }
            ];
        }
        $lists = AdminUser::where($where)
            ->with('role')
            ->orderBy('created_at', 'DESC')
            ->paginate();

        $lists->setCollection($lists->getCollection()
            ->makeHidden(['role'])
            ->each(function (Model $q) {
                $q->append('role_name');
            }));

        //获取下级角色
        $role_list = AdminRole::where($role_where)
            ->select()
            ->get();

        return $this->returnSuccessJson(compact('lists', 'role_list'));
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}