<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/6
 * Time: 22:50.
 */
declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Application\Admin\Model\UploadFile;
use App\Application\Admin\Model\UploadFileGroup;
use App\Application\Admin\Service\AdminSettingService;
use App\Application\Admin\Service\AdminUserService;
use App\Application\Admin\Service\UploadService;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[Middlewares([SessionMiddleware::class,AdminMiddleware::class])]
#[Controller("admin/upload")]
class UploadController extends AbstractController
{

    #[Inject]
    protected AdminSettingService $setting;

    /**
     * 移动文件
     */
    #[Api]
    #[PostMapping("/component/upload/file/move")]
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
     */
    #[Api]
    #[PostMapping("/component/upload/file/delete")]
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
     */
    #[Api]
    #[GetMapping("/component/upload/file/lists")]
    function fileList()
    {
        $file_type = $this->request->input('file_type', UploadFile::FILE_TYPE_IMAGE);
        $group_id = (int)$this->request->input('group_id', -1);
        $acl = $this->request->input('acl', UploadFile::ACL_DEFAULT);
        $admin_user_id = AdminUserService::getInstance()
            ->getAdminUserId();
        $where = [
            ['upload_user_id', '=', $admin_user_id],//默认只显示管理后台上传
            ['upload_user_type', '=', UploadFile::USER_TYPE_ADMIN],//默认只显示管理后台上传
            ['acl', '=', $acl],
            ['file_type', '=', $file_type]
        ];
        if ($group_id !== -1) {
            $where[] = ['group_id', '=', $group_id];
        }

        $lists = UploadFile::where($where)
            ->orderBy('file_id', 'DESC')
            ->select([
                'file_drive',
                'file_id',
                'file_name',
                'file_path',
                'file_size',
                'file_type',
                'file_url',
                'file_thumb',
                'group_id',
                'file_ext',
                'acl'
            ])
            ->paginate();

        return compact('lists');
    }

    /**
     * 上传文件
     */
    #[Api]
    #[RequestMapping("/component/upload/save")]
    function fileSave()
    {
        $post_data = $this->request->post();
        $group_id = (int)$this->request->input('group_id', 0);
        $file_type = $this->request->input('file_type', UploadFile::FILE_TYPE_IMAGE);
        $upload_service = new UploadService(null, $file_type);
        $admin_user_id = AdminUserService::getInstance()
            ->getAdminUserId();
        $upload_file = $upload_service->setUserId($admin_user_id)
            ->setUserType('admin')
            ->setGroupId($group_id)
            ->save($post_data);

        return compact('upload_file');
    }

    /**
     * 上传文件
     */
    #[Api]
    #[RequestMapping("/component/upload/file")]
    function fileUpload()
    {
        $file = $this->request->file('file');
        $group_id = (int)$this->request->input('group_id', 0);
        $file_type = $this->request->input('file_type', UploadFile::FILE_TYPE_IMAGE);
        $upload_service = new UploadService($file, $file_type);
        $admin_user_id = AdminUserService::getInstance()
            ->getAdminUserId();
        $upload_file = $upload_service->setUserId($admin_user_id)
            ->setUserType('admin')
            ->setGroupId($group_id)
            ->save();

        return compact('upload_file');
    }

    /**
     * 删除分组
     */
    #[Api]
    #[PostMapping("/component/upload/group/delete")]
    function groupDelete()
    {
        $group_id = $this->request->post('group_id', 0);
        $file_group = UploadFileGroup::firstOrNew([
            'group_id' => $group_id,
            'upload_user_type' => 'admin'
        ]);
        if ($file_group->group_id) {
            return $file_group->delete() ? $this->returnSuccessJson() : $this->returnErrorJson();
        } else {
            return $this->returnErrorJson('找不到该记录');
        }
    }

    /**
     * 分组列表
     */
    #[Api]
    #[GetMapping("/component/upload/group/lists")]
    function groupList()
    {
        $file_type = $this->request->input('file_type', UploadFile::FILE_TYPE_IMAGE);
        $admin_user_id = AdminUserService::getInstance()
            ->getAdminUserId();
        $where = [
            ['file_type', '=', $file_type],
            ['upload_user_type', '=', UploadFile::USER_TYPE_ADMIN],
            ['upload_user_id', '=', $admin_user_id],
        ];
        $group_list = UploadFileGroup::where($where)
            ->orderByDesc('group_id')
            ->get();

        $upload_file_size = $this->setting->getUploadSetting('upload_file_size', 2048);
        $upload_allow_ext = explode("|", $this->setting->getUploadSetting('upload_allow_ext', ''));
        $upload_drive = $this->setting->getUploadSetting('upload_drive', UploadFile::UPLOAD_DRIVE_LOCAL);
        $upload_setting = compact('upload_allow_ext', 'upload_file_size');
        $upload_service = new UploadService();
        try {
            //获取第三方直传所需的form配置
            $acl = $this->request->input('acl', 'default');
            $upload_form = $upload_service->getUploadForm($acl);
        } catch (\Throwable $exception) {
            $upload_form = [];
            $upload_drive = UploadFile::UPLOAD_DRIVE_LOCAL;
        }

        return compact('upload_setting', 'group_list', 'upload_drive', 'upload_form');
    }

    /**
     * 新增/编辑分组
     */
    #[Api]
    #[PostMapping("/component/upload/group")]
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
        $admin_user_id = AdminUserService::getInstance()
            ->getAdminUserId();
        $file_group = UploadFileGroup::firstOrNew([
            'group_id' => $group_id,
            'file_type' => $file_type,
            'upload_user_type' => UploadFile::USER_TYPE_ADMIN,
            'upload_user_id' => $admin_user_id,
        ]);
        $file_group->group_name = $group_name;
        $file_group->file_type = $file_type;

        return $file_group->save() ? $this->returnSuccessJson(compact('file_group')) : $this->returnErrorJson();
    }

    /**
     * 上传配置
     */
    #[Api]
    #[GetMapping("setting/info")]
    function settingInfo()
    {
        $setting = $this->setting->getUploadSetting();
        $driver_list = UploadFile::getDriverList();

        return compact('driver_list', 'setting');
    }

    /**
     * 修改上传配置
     */
    #[Api]
    #[PostMapping("setting")]
    function settingSubmit()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->setting->setUploadSetting($setting);

        return $res ? self::returnSuccessJson() : self::returnErrorJson();
    }

    #[Api]
    #[GetMapping("index/lists")]
    function lists()
    {
        $where = [];
        $file_name = $this->request->input('file_name', '');
        $file_type = $this->request->input('file_type', '');
        if ($file_type !== '') {
            $where[] = ['file_type', '=', $file_type];
        }
        if ($file_name !== '') {
            $where[] = ['file_name', 'like', "%{$file_name}%"];
        }
        $lists = UploadFile::where($where)
            ->orderBy('file_id', 'DESC')
            ->paginate();

        return compact('lists');
    }

    /**
     * 上传配置页面
     */
    #[View]
    #[GetMapping("setting")]
    function setting()
    {
    }

    /**
     * 文件列表页面
     */
    #[View]
    #[GetMapping("index")]
    function index()
    {
    }
}