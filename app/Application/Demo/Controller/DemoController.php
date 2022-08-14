<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/26
 * Time: 11:46.
 */

declare(strict_types=1);

namespace App\Application\Demo\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Controller\AdminAbstractController;
use App\Application\Demo\Model\DemoUser;
use App\Application\Demo\Service\DemoSettingService;
use App\Application\Demo\Service\QueueService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Qbhy\HyperfAuth\Annotation\Auth;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="/demo/demo")
 */
class DemoController extends AdminAbstractController
{

    /**
     * @Inject()
     */
    protected DemoSettingService $demo_setting;

    /**
     * @Inject()
     */
    protected DemoUser $demo_user;

    /**
     * @Auth("api_auth")
     * @Api()
     * @PostMapping(path="auth/login")
     */
    function authLogin()
    {
        $user = $this->demo_user->getLoginInfo();

        return compact('user');
    }

    /**
     * @Api()
     * @PostMapping(path="auth/submit")
     */
    function authSubmit()
    {
        $username = $this->request->post('username', '');
        $password = $this->request->post('password', '');
        $token = DemoUser::loginEvent($username, $password);

        return compact('token');
    }

    /**
     * @View()
     * @GetMapping(path="auth")
     */
    function auth() { }

    /**
     * 示例队列消息生成
     * @Api()
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

        return compact('type');
    }

    /**
     * @Api()
     * @PostMapping(path="setting")
     */
    function settingSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->demo_setting->setDemoSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @Api()
     * @GetMapping(path="setting/info")
     */
    function settingInfo()
    {
        $setting = $this->demo_setting->getDemoSetting();

        return compact('setting');
    }

    /**
     * @View()
     * @GetMapping(path="setting")
     */
    function setting() { }

    /**
     * @View()
     * @GetMapping(path="queue")
     */
    function queue() { }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        return ['title' => '编辑示例页面'];
    }

    /**
     * @View()
     * @GetMapping(path="lists")
     */
    function lists() { }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return ['msg' => $this->request->input('msg', '')];
    }
}