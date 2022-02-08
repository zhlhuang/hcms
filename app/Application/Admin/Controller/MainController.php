<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/main")
 */
class MainController extends AdminAbstractController
{
    /**
     * @View()
     * @GetMapping(path="demo")
     */
    function demo()
    {
        return RenderParam::display('index', ['msg' => 'demo']);
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display('index', ['msg' => 'index']);
    }
}
