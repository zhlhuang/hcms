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

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        return RenderParam::display('edit', ['title' => '编辑示例页面']);
    }

    /**
     * @View()
     * @GetMapping(path="lists")
     */
    function lists()
    {
        return RenderParam::display();
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display('view', ['msg' => $this->request->input('msg', '')]);
    }
}