<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\Admin\Model\Access;
use App\Application\Admin\RequestParam\AccessSubmitRequestParam;
use App\Application\Admin\Service\AccessService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PutMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Middlewares([SessionMiddleware::class,AdminMiddleware::class])]
#[Controller("admin/access")]
class AccessController extends AbstractController
{

    #[Api]
    #[DeleteMapping("delete/{access_id}")]
    function delete(int $access_id)
    {
        $access = Access::find($access_id);
        if (!$access) {
            return $this->returnErrorJson('找不到该记录');
        }

        //如果有下级菜单，不能删除
        if (Access::where('parent_access_id', $access->access_id)
                ->count() > 0) {
            return $this->returnErrorJson('该菜单有下级菜单，不能删除');
        }

        return $access->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    #[Api]
    #[RequestMapping("edit", ["POST", "PUT"])]
    function submitEdit()
    {
        $request_param = new AccessSubmitRequestParam();
        $request_param->validatedThrowMessage();

        $access_id = $request_param->getAccessId();
        $parent_access_id = $request_param->getParentAccessId();
        if ($parent_access_id > 0 && $access_id === $parent_access_id) {
            return $this->returnErrorJson('上级菜单不能是自己或自己的下级');
        }
        $access = Access::updateOrCreate(['access_id' => $access_id], [
            'parent_access_id' => $parent_access_id,
            'access_name' => $request_param->getAccessName(),
            'uri' => $request_param->getUri(),
            'params' => $request_param->getParams(),
            'sort' => $request_param->getSort(),
            'is_menu' => $request_param->getIsMenu(),
            'menu_icon' => $request_param->getMenuIcon(),
        ]);

        return $access ? $this->returnSuccessJson(compact('access')) : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("edit/{access_id}")]
    function editInfo(int $access_id = 0)
    {
        //获取菜单
        $access_list = Access::where('parent_access_id', 0)
            ->with(['children'])
            ->select()
            ->get();

        $access = Access::where('access_id', $access_id)
            ->first() ?: [];

        return compact('access_list', 'access');
    }

    #[View]
    #[GetMapping("edit")]
    function edit()
    {
        $access_id = (int)$this->request->input('access_id', 0);

        return ['title' => $access_id > 0 ? '编辑菜单与权限' : '新增菜单与权限'];
    }

    #[Api]
    #[PutMapping("index/sort/{access_id}")]
    function sort(int $access_id)
    {
        $access = Access::where('access_id', $access_id)
            ->first();
        if (!$access) {
            return $this->returnErrorJson('找不到该记录');
        }
        $access->sort = $this->request->input('sort', 100);

        return $access->save() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("index/lists")]
    function lists()
    {
        $lists = AccessService::getInstance()
            ->getAccessByRoleId();

        return compact('lists');
    }

    #[View]
    #[GetMapping("index")]
    function index()
    {
    }
}
