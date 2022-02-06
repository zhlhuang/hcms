<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Model\Access;
use Hyperf\Database\Model\Relations\Relation;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/admin/access")
 */
class AccessController extends AdminAbstractController
{

    /**
     * @PostMapping(path="delete")
     */
    function delete()
    {
        $access_id = $this->request->post('access_id', 0);
        $access = Access::find($access_id);
        if (!$access) {
            return $this->returnSuccessError('找不到该记录');
        }

        //如果有下级菜单，不能删除
        if (Access::where('parent_access_id', $access->access_id)
                ->count() > 0) {
            return $this->returnSuccessError('该菜单有下级菜单，不能删除');
        }

        return $access->delete() ? $this->returnSuccessJson() : $this->returnSuccessError();
    }

    /**
     * @PostMapping(path="edit")
     */
    function submitEdit()
    {
        $access_id = (int)$this->request->post('access_id', 0);
        $parent_access_id = (int)$this->request->post('parent_access_id', 0);
        if ($parent_access_id > 0 && $access_id === $parent_access_id) {
            return $this->returnSuccessError('上级菜单不能是自己或自己的下级');
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

        return $access ? $this->returnSuccessJson(compact('access')) : $this->returnSuccessError();
    }

    /**
     * @GetMapping(path="edit/info")
     */
    function editInfo()
    {
        //获取菜单
        $access_list = Access::where('parent_access_id', 0)
            ->with(['children'])
            ->select()
            ->get();

        $access = Access::where('access_id', $this->request->input('access_id', 0))
            ->first() ?: [];

        return $this->returnSuccessJson(compact('access_list', 'access'));
    }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        return RenderParam::display('edit', ['title' => '新增菜单与权限']);
    }

    /**
     * @PostMapping(path="index/sort")
     */
    function sort()
    {
        $access_id = $this->request->input('access_id', 0);
        $access = Access::where('access_id', $access_id)
            ->first();
        if (!$access) {
            return $this->returnSuccessError('找不到该记录');
        }
        $access->sort = $this->request->input('sort', 100);

        return $access->save() ? $this->returnSuccessJson() : $this->returnSuccessError();
    }

    /**
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $lists = Access::where('parent_access_id', 0)
            ->with([
                'children' => function (Relation $relation) {
                    $relation->with(['children'])
                        ->orderBy('sort');
                }
            ])
            ->orderBy('sort')
            ->select()
            ->get();

        return $this->returnSuccessJson(compact('lists'));
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display();
    }
}
