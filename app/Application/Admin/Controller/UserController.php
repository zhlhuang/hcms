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
use App\Application\Admin\RequestParam\UserResetRequestParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\Admin\Model\AdminLoginRecord;
use App\Application\Admin\Model\AdminRole;
use App\Application\Admin\Model\AdminUser;
use App\Application\Admin\RequestParam\UserSubmitRequestParam;
use App\Application\Admin\Service\AdminUserService;
use App\Controller\AbstractController;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\DbConnection\Model\Model;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Middlewares([SessionMiddleware::class, AdminMiddleware::class])]
#[Controller("admin/user")]
class UserController extends AbstractController
{
    #[Api]
    #[PostMapping('reset')]
    public function resetSubmit()
    {
        $user_reset_request = new UserResetRequestParam();
        $user_reset_request->validatedThrowMessage();

        if ($user_reset_request->getNewPassword() != $user_reset_request->getNewConfirmPassword()) {
            return $this->returnErrorJson('两次新密码输入不一致');
        }
        $username = AdminUserService::getInstance()
            ->getAdminUser()->username;
        $admin_user = AdminUser::where(['username' => $username])
            ->first();
        if ($admin_user instanceof AdminUser && $admin_user->passwordVerify($user_reset_request->getPassword())) {
            $admin_user->password = AdminUser::makePassword($username, $user_reset_request->getNewPassword());

            return $admin_user->save() ? [] : $this->returnErrorJson();
        } else {
            return $this->returnErrorJson('账号或密码错误');
        }
    }

    #[Api]
    #[GetMapping('reset/info')]
    public function resetInfo()
    {
        $admin_user = AdminUserService::getInstance()
            ->getAdminUser();

        return compact('admin_user');
    }

    #[View]
    #[GetMapping]
    public function reset()
    {

    }

    #[Api]
    #[DeleteMapping("record/delete/{id}")]
    public function recordDelete(int $id)
    {
        $record = AdminLoginRecord::find($id);
        if (!$record) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $record->delete() ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("record/lists")]
    public function recordLists()
    {
        $where = [];
        $username = trim($this->request->input('username', ''));
        $ip = trim($this->request->input('ip', ''));
        if ($username != '') {
            $where[] = ['username', '=', $username];
        }
        if ($ip != '') {
            $where[] = ['ip', '=', $ip];
        }
        $lists = AdminLoginRecord::where($where)
            ->orderByDesc('id')
            ->paginate();

        return compact('lists');
    }

    #[View]
    #[GetMapping]
    public function record()
    {
    }

    #[Api]
    #[DeleteMapping("delete/{admin_user_id}")]
    function delete(int $admin_user_id)
    {
        $admin_user = AdminUser::find($admin_user_id);
        if (!$admin_user) {
            return $this->returnErrorJson('找不到该记录');
        }

        $role_ids = AdminRole::getChildRoleIds(AdminUserService::getInstance()
            ->getAdminUserRoleId());
        if (!in_array($admin_user->role_id, $role_ids)) {
            return $this->returnErrorJson("没有权限操作");
        }

        return $admin_user->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }


    #[Api]
    #[RequestMapping("edit", ["POST", "PUT"])]
    function submitEdit()
    {
        $request_param = new UserSubmitRequestParam();
        $request_param->validatedThrowMessage();
        /**
         * @var AdminUser
         */
        $admin_user = AdminUser::firstOrNew([
            'admin_user_id' => $request_param->getAdminUserId()
        ]);
        $password = $request_param->getPassword();
        $username = $request_param->getUsername();
        $real_name = $request_param->getRealName();
        if ($admin_user->admin_user_id === 0 && $password === '') {
            return $this->returnErrorJson('请输入密码');
        }
        if ($password !== '' || !$admin_user->admin_user_id) {
            $admin_user->password = AdminUser::makePassword($username, $password);
        }
        $role_id = $request_param->getRoleId();
        $res = $admin_user->createAdminUser($username, $password, $real_name, $role_id);

        return $res ? $this->returnSuccessJson(compact('admin_user')) : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("edit/{admin_user_id}")]
    function editInfo(int $admin_user_id)
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
        $admin_user = AdminUser::where('admin_user_id', $admin_user_id)
            ->select(['admin_user_id', 'role_id', 'real_name', 'username'])
            ->first() ?: [];

        return compact('role_list', 'admin_user');
    }

    #[View]
    #[GetMapping("edit")]
    function edit()
    {
        $admin_user_id = (int)$this->request->input('admin_user_id', 0);

        return ['title' => $admin_user_id > 0 ? '编辑管理员' : '新增管理员'];
    }

    #[Api]
    #[GetMapping("index/lists")]
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

        return compact('lists', 'role_list');
    }

    #[View]
    #[GetMapping("index")]
    function index()
    {
    }
}