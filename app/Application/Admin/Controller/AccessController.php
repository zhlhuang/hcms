<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Model\Access;
use App\Application\Admin\Service\AccessService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/access")
 */
class AccessController extends AbstractController
{

    /**
     * @Api()
     * @PostMapping(path="delete")
     */
    function delete()
    {
        $access_id = $this->request->post('access_id', 0);
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

    /**
     * @Api()
     * @PostMapping(path="edit")
     */
    function submitEdit()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'access_name' => 'required',
            'uri' => 'required',
        ], [
            'access_name.required' => '请输入菜单/权限名称',
            'uri.required' => '请输入uri',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }

        $access_id = (int)$this->request->post('access_id', 0);
        $parent_access_id = (int)$this->request->post('parent_access_id', 0);
        if ($parent_access_id > 0 && $access_id === $parent_access_id) {
            return $this->returnErrorJson('上级菜单不能是自己或自己的下级');
        }
        $access = Access::updateOrCreate(['access_id' => $access_id], [
            'parent_access_id' => $parent_access_id,
            'access_name' => $this->request->post('access_name', ''),
            'uri' => $this->request->post('uri', ''),
            'params' => $this->request->post('params', ''),
            'sort' => (int)$this->request->post('sort', 100),
            'is_menu' => (int)$this->request->post('is_menu', Access::IS_MENU_YES),
            'menu_icon' => $this->request->post('menu_icon', ''),
        ]);

        return $access ? $this->returnSuccessJson(compact('access')) : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="edit/{access_id}")
     */
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

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        $access_id = (int)$this->request->input('access_id', 0);

        return ['title' => $access_id > 0 ? '编辑菜单与权限' : '新增菜单与权限'];
    }

    /**
     * @Api()
     * @PostMapping(path="index/sort")
     */
    function sort()
    {
        $access_id = $this->request->input('access_id', 0);
        $access = Access::where('access_id', $access_id)
            ->first();
        if (!$access) {
            return $this->returnErrorJson('找不到该记录');
        }
        $access->sort = $this->request->input('sort', 100);

        return $access->save() ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $lists = AccessService::getInstance()
            ->getAccessByRoleId();

        return compact('lists');
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}
