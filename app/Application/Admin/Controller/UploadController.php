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
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\UploadFileGroup;
use App\Application\Admin\Service\AdminSettingService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/upload")
 */
class UploadController extends AdminAbstractController
{
    /**
     * @PostMapping(path="group/delete")
     */
    function groupDelete()
    {
        $group_id = $this->request->post('group_id', 0);
        $file_group = UploadFileGroup::firstOrNew([
            'group_id' => $group_id
        ]);
        if ($file_group->group_id) {
            return $file_group->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
        } else {
            return $this->returnErrorJson('找不到该记录');
        }
    }

    /**
     * @GetMapping(path="group/lists")
     */
    function groupList()
    {
        $file_type = $this->request->post('file_type', UploadFileGroup::FILE_TYPE_IMAGE);
        $where = [
            ['file_type', '=', $file_type]
        ];
        $group_list = UploadFileGroup::where($where)
            ->get();

        return $this->returnSuccessJson(compact('group_list'));
    }

    /**
     * @PostMapping(path="group")
     */
    function groupEdit()
    {
        $validator = $this->validationFactory->make($this->request->post(), [
            'group_name' => 'required',
        ], [
            'group_name.required' => '分组名称不能为空',
        ]);

        if ($validator->fails()) {
            return $this->returnErrorJson($validator->errors()
                ->first());
        }
        $group_id = $this->request->post('group_id', 0);
        $group_name = $this->request->post('group_name', '');
        $file_type = $this->request->post('file_type', UploadFileGroup::FILE_TYPE_IMAGE);
        $file_group = UploadFileGroup::firstOrNew([
            'group_id' => $group_id
        ]);
        $file_group->group_name = $group_name;
        $file_group->file_type = $file_type;

        return $file_group->save() ? $this->returnSuccessJson(compact('file_group')) : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="setting/info")
     */
    function settingInfo()
    {
        $setting = AdminSettingService::getUploadSetting();

        return self::returnSuccessJson(compact('setting'));
    }

    /**
     * @PostMapping(path="setting")
     */
    function settingSubmit()
    {
        $setting = $this->request->post('setting', []);
        $res = AdminSettingService::setUploadSetting($setting);

        return $res ? self::returnSuccessJson() : self::returnErrorJson();
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
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display();
    }
}