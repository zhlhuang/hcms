<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/26
 * Time: 11:46.
 */

declare(strict_types=1);

namespace App\Application\Demo\Controller;

use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Admin\Lib\RenderParam;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/demo/demo")
 */
class DemoController extends AdminAbstractController
{
    function index()
    {
        return "hello demo";
    }

    /**
     * @GetMapping(path="view")
     * @View()
     */
    function view()
    {
        return RenderParam::display('view', ['msg' => "hello demo " . $this->request->input('msg', '')]);
    }
}