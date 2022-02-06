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

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/admin/index")
 */
class IndexController extends AdminAbstractController
{

    private function getMenuList($parent_access_id = 0)
    {
        $menu_list = Access::where('parent_access_id', $parent_access_id)
            ->where('is_menu', Access::IS_MENU_YES)
            ->orderBy('sort')
            ->select([
                'menu_icon as icon',
                'access_name as name',
                'parent_access_id as pid',
                'uri',
                'params',
                'access_id'
            ])
            ->get()
            ->toArray();
        if (empty($menu_list)) {
            return [];
        }
        foreach ($menu_list as &$item) {
            $item['url'] = url($item['uri'], $item['params']);
            $item['items'] = $this->getMenuList($item['access_id']);
            $item['id'] = md5($item['url'] . $item['access_id']);
        }

        return $menu_list;
    }

    /**
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $menu_list = $this->getMenuList();

        return $this->returnSuccessJson(compact('menu_list'));
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
//        $menu_list = $this->getMenuList();

        return RenderParam::display();
    }
}
