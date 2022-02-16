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
use App\Application\Demo\Service\QueueService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/demo/demo")
 */
class DemoController extends AdminAbstractController
{


    /**
     * @PostMapping(path="queue")
     */
    function setQueueMessage()
    {
        $type = $this->request->input('type', 'delay');
        $q = new QueueService();
        if ($type === 'delay') {
            $q->setDelayMessage(['id' => uniqid(), 'msg' => 'delay']);
        }
        if ($type === 'long') {
            $q->setLongMessage(['id' => uniqid(), 'msg' => 'long']);
        }
        if ($type === 'error') {
            $q->setErrorMessage(['id' => uniqid(), 'msg' => 'error']);
        }

        return $this->returnSuccessJson(compact('type'));
    }

    /**
     * @View()
     * @GetMapping(path="queue")
     */
    function queue()
    {
        return RenderParam::display();
    }

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