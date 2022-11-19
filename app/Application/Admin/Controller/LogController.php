<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 18:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use App\Application\Admin\Service\AdminSettingService;
use App\Controller\AbstractController;
use App\Exception\ErrorException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/log")
 */
class LogController extends AbstractController
{
    /**
     * @Inject()
     */
    protected AdminSettingService $setting;

    /**
     * @Inject()
     */
    protected ConfigInterface $config;


    /**
     * @Api()
     * @PostMapping(path="index/delete/{file_name}")
     */
    function deleteLog($file_name = '')
    {
        //为了安全起见，会把文件名含有/的符号替换掉。
        $file_name = str_replace('/', '', trim($file_name));
        $file_type = explode('-', $file_name)[0] ?? 'request';
        $request_log_config = $this->config->get("logger.{$file_type}", []);
        $log_filename = $request_log_config['handler']['constructor']['filename'] ?? '';
        $file_dir = str_replace("{$file_type}.log", '', $log_filename);
        if (!file_exists($file_dir . $file_name)) {
            throw new ErrorException('抱歉，找不到该文件');
        }

        return unlink($file_dir . $file_name) ? $this->returnSuccessJson() : $this->returnErrorJson();
    }

    /**
     * @GetMapping(path="index/detail")
     */
    function detail()
    {
        $file_name = $this->request->input('file');
        $file_name = str_replace('/', '', trim($file_name));
        $file_type = explode('-', $file_name)[0] ?? 'request';
        $request_log_config = $this->config->get("logger.{$file_type}", []);
        $log_filename = $request_log_config['handler']['constructor']['filename'] ?? '';
        $file_dir = str_replace("{$file_type}.log", '', $log_filename);
        if (!file_exists($file_dir . $file_name)) {
            throw new ErrorException('抱歉，找不到该文件');
        }

        return $this->response->raw(file_get_contents($file_dir . $file_name));
    }

    /**
     * @Api()
     * @GetMapping(path="index/setting/info")
     */
    function settingInfo()
    {
        $log_type = $this->request->input('log_type', 'request');
        $request_log_config = $this->config->get('logger.' . $log_type, []);
        $log_filename = $request_log_config['handler']['constructor']['filename'] ?? '';
        $file_dir = str_replace($log_type . '.log', '', $log_filename);
        if (!is_dir($file_dir)) {
            mkdir($file_dir, 0700, true);
        }
        $scan_list = scandir($file_dir, 1);
        $file_list = [];
        foreach ($scan_list as $file) {
            if ($file !== '.' && $file !== '..') {
                $file_path = $file_dir . $file;
                $file_list[] = [
                    'file_name' => $file,
                    'file_path' => $file_path,
                    'file_size' => sprintf('%0.2fKB', filesize($file_path) / 1024)
                ];
            }
        }
        $setting = $this->setting->getLogSetting();

        return compact('setting', 'file_list');
    }

    /**
     * @Api()
     * @PostMapping(path="index/setting")
     */
    function settingSave()
    {
        $setting = $this->request->post('setting', []);
        $res = $this->setting->setLogSetting($setting);

        return $res ? $this->returnSuccessJson(compact('setting')) : $this->returnErrorJson();
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index() { }
}