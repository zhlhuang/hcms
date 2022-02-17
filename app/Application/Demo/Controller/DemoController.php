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
use App\Application\Demo\Service\DemoSettingService;
use App\Application\Demo\Service\QueueService;
use Hyperf\Di\Annotation\Inject;
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
     * @Inject()
     * @var DemoSettingService
     */
    protected $demo_setting;

    /**
     * 示例队列消息生成
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
     * @PostMapping(path="setting")
     */
    function settingSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->demo_setting->setDemoSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="setting/info")
     */
    function settingInfo()
    {
        $setting = $this->demo_setting->getDemoSetting();

        return $this->returnSuccessJson(compact('setting'));
    }

    /**
     * @View()
     * @GetMapping(path="setting")
     */
    function setting()
    {
        return RenderParam::display();
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