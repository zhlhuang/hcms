<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Service\AccessService;
use App\Application\Admin\Service\AdminUserService;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Middlewares([SessionMiddleware::class,AdminMiddleware::class])]
#[Controller("/admin/index")]
class IndexController extends AbstractController
{
    #[Api]
    #[GetMapping("index/lists")]
    function lists()
    {
        $menu_list = AccessService::getInstance()
            ->getMenuByRoleId(AdminUserService::getInstance()
                ->getAdminUserRoleId());
        $admin_user = AdminUserService::getInstance()
            ->getAdminUser();

        return compact('menu_list', 'admin_user');
    }

    #[View]
    #[GetMapping("index")]
    function index()
    {
    }

    /**
     * 根据项目实际情况需要修改该路由
     */
    #[RequestMapping("/")]
    function root()
    {
        return $this->response->redirect(url('admin/index/index'));
    }
}
