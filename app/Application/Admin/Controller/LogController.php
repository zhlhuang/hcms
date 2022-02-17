<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 18:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Service\AdminSettingService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/log")
 */
class LogController extends AdminAbstractController
{
    /**
     * @Inject()
     * @var AdminSettingService
     */
    protected $setting;

    /**
     * @GetMapping(path="setting/info")
     */
    function siteInfo()
    {
        $setting = $this->setting->getLogSetting();

        return $this->returnSuccessJson(compact('setting'));
    }

    /**
     * @PostMapping(path="setting")
     */
    function siteSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->setting->setLogSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
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