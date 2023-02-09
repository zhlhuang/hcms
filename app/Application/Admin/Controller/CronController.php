<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Model\CronLog;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;


#[Middleware(AdminMiddleware::class)]
#[Controller(prefix: "/admin/cron")]
class CronController extends AbstractController
{

    #[Api]
    #[DeleteMapping("delete")]
    public function deleteByTime()
    {
        $time = $this->request->input('time', '');

        if (!$time) {
            return $this->returnErrorJson('请选择时间');
        }
        $res = CronLog::where('created_at', '<', $time)
            ->delete();

        return $res ? [] : $this->returnErrorJson();
    }


    #[Api]
    #[DeleteMapping("delete/{log_id}")]
    public function delete(int $log_id)
    {
        $log = CronLog::find($log_id);
        if (!$log) {
            return $this->returnErrorJson('找不到该记录');
        }

        return $log->delete() ? [] : $this->returnErrorJson();
    }

    #[Api]
    #[GetMapping("lists")]
    public function getList()
    {
        $where = [];
        $task_class = $this->request->input('task_class', '');
        if ($task_class != '') {
            $where[] = ['task_class', 'like', "%{$task_class}%"];
        }
        $lists = CronLog::where($where)
            ->orderByDesc('created_at')
            ->paginate();

        return compact('lists');
    }

    #[View]
    #[GetMapping("")]
    public function index()
    {
    }
}
