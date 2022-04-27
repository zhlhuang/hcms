<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/6
 * Time: 22:50.
 */
declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\Setting;
use App\Application\Admin\Service\AdminSettingService;
use App\Service\SettingService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/setting")
 */
class SettingController extends AdminAbstractController
{

    /**
     * @Inject()
     */
    protected AdminSettingService $setting;

    /**
     * @GetMapping(path="site/info")
     */
    function siteInfo()
    {
        $setting = $this->setting->getSiteSetting();

        return $this->returnSuccessJson(compact('setting'));
    }

    /**
     * @PostMapping(path="site")
     */
    function siteSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->setting->setSiteSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="index/lists")
     */
    function lists()
    {
        $where = [];
        $keyword = $this->request->input('keyword', '');
        $search_setting_group = $this->request->input('setting_group', '');
        if ($keyword) {
            $where[] = ['setting_key', 'like', "%{$keyword}%"];
        }
        if ($search_setting_group !== '') {
            $where[] = ['setting_group', '=', $search_setting_group];
        }
        $lists = Setting::where($where)
            ->orderBy('setting_id', 'DESC')
            ->paginate();
        $setting_group = Setting::where([])
            ->distinct()
            ->pluck('setting_group')
            ->toArray();

        return self::returnSuccessJson(compact('lists', 'setting_group'));
    }

    /**
     * @PostMapping(path="edit")
     */
    function editSubmit()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'setting_key' => 'required',
            'setting_value' => 'required',
            'type' => 'required',
        ], [
            'setting_key.required' => '请输入配置的key',
            'setting_value.required' => '请输入配置的值',
            'type.required' => '请选择配置类型',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }

        $setting_id = (int)$this->request->post('setting_id', 0);
        $setting = Setting::updateOrCreate(['setting_id' => $setting_id], [
            'setting_key' => $this->request->post('setting_key', ''),
            'setting_value' => $this->request->post('setting_value', ''),
            'setting_description' => $this->request->post('setting_description', ''),
            'setting_group' => $this->request->post('setting_group', ''),
            'type' => $this->request->post('type', Setting::TYPE_STRING),
        ]);

        //清空指定分组的缓存
        SettingService::getInstance()
            ->flushCache($setting->setting_group);

        return $setting ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="edit/info")
     */
    function editInfo()
    {
        $setting_id = (int)$this->request->input('setting_id', 0);
        $setting = Setting::find($setting_id) ?: [];

        return self::returnSuccessJson(compact('setting'));
    }

    /**
     * @View()
     * @GetMapping(path="site")
     */
    function site() { }

    /**
     * @View()
     * @GetMapping(path="edit")
     */
    function edit()
    {
        $setting_id = (int)$this->request->input('setting_id', 0);

        return ['title' => $setting_id > 0 ? '编辑配置' : '新增配置'];
    }


    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}