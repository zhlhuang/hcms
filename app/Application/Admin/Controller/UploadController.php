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
use App\Application\Admin\Model\UploadFile;
use App\Application\Admin\Model\UploadFileGroup;
use App\Application\Admin\Service\AdminSettingService;
use App\Application\Admin\Service\UploadService;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/upload")
 */
class UploadController extends AdminAbstractController
{
    /**
     * 删除文件
     * @PostMapping(path="file/move")
     */
    function fileMove()
    {
        $selected_file_ids = $this->request->post('selected_file_ids', []);
        $group_id = (int)$this->request->post('group_id', 0);
        if (!is_array($selected_file_ids) || empty($selected_file_ids)) {
            return $this->returnErrorJson('请选择你要移动的文件。');
        }
        $res = UploadFile::whereIn('file_id', $selected_file_ids)
            ->update([
                'group_id' => $group_id
            ]);

        return $res ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * 删除文件
     * @PostMapping(path="file/delete")
     */
    function fileDelete()
    {
        $selected_file_ids = $this->request->post('selected_file_ids', []);
        if (!is_array($selected_file_ids) || empty($selected_file_ids)) {
            return $this->returnErrorJson('请选择你要删除的文件。');
        }
        $res = UploadFile::whereIn('file_id', $selected_file_ids)
            ->delete();

        return $res ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * 文件列表
     * @GetMapping(path="file/lists")
     */
    function fileList()
    {
        $file_type = $this->request->input('file_type', UploadFile::FILE_TYPE_IMAGE);
        $group_id = (int)$this->request->input('group_id', -1);
        $where = [
            ['file_type', '=', $file_type]
        ];
        if ($group_id !== -1) {
            $where[] = ['group_id', '=', $group_id];
        }

        $lists = UploadFile::where($where)
            ->orderBy('file_id', 'DESC')
            ->select([
                'file_id',
                'file_name',
                'file_path',
                'file_size',
                'file_type',
                'file_url',
                'file_thumb',
                'group_id',
                'file_ext'
            ])
            ->paginate();

        return $this->returnSuccessJson(compact('lists'));
    }

    /**
     * 上传文件
     * @RequestMapping(path="file")
     */
    function fileUpload()
    {
        $file = $this->request->file('file');
        $group_id = (int)$this->request->input('group_id', 0);
        $upload_service = new UploadService($file);
        $upload_file = $upload_service->setUserId($this->getAdminUserId())
            ->setUserType('admin')
            ->setGroupId($group_id)
            ->save();

        return $this->returnSuccessJson(compact('upload_file'));
    }

    /**
     * 删除分组
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
     * 分组列表
     * @GetMapping(path="group/lists")
     */
    function groupList()
    {
        $file_type = $this->request->post('file_type', UploadFile::FILE_TYPE_IMAGE);
        $where = [
            ['file_type', '=', $file_type]
        ];
        $group_list = UploadFileGroup::where($where)
            ->get();

        return $this->returnSuccessJson(compact('group_list'));
    }

    /**
     * 新增/编辑分组
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
        $file_type = $this->request->post('file_type', UploadFile::FILE_TYPE_IMAGE);
        $file_group = UploadFileGroup::firstOrNew([
            'group_id' => $group_id
        ]);
        $file_group->group_name = $group_name;
        $file_group->file_type = $file_type;

        return $file_group->save() ? $this->returnSuccessJson(compact('file_group')) : $this->returnErrorJson();
    }

    /**
     * 上传配置
     * @GetMapping(path="setting/info")
     */
    function settingInfo()
    {
        $setting = AdminSettingService::getUploadSetting();

        return self::returnSuccessJson(compact('setting'));
    }

    /**
     * 修改上传配置
     * @PostMapping(path="setting")
     */
    function settingSubmit()
    {
        $setting = $this->request->post('setting', []);
        $res = AdminSettingService::setUploadSetting($setting);

        return $res ? self::returnSuccessJson() : self::returnErrorJson();
    }

    /**
     * 上传配置页面
     * @View()
     * @GetMapping(path="setting")
     */
    function setting()
    {
        return RenderParam::display();
    }

    /**
     * 文件列表页面
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display();
    }
}