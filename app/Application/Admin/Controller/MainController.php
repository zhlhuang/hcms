<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middleware;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Redis\Redis;

/**
 * @Middleware(AdminMiddleware::class)
 * @Controller(prefix="admin/main")
 */
class MainController extends AdminAbstractController
{
    /**
     * @Inject()
     */
    protected Redis $redis;

    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        try {
            $mysql_version = json_decode(json_encode(Db::select("select version()")[0]), true)['version()'] ?? '';
        } catch (\Exception $exception) {
            $mysql_version = $exception->getMessage();
        }
        try {
            $redis_version = $this->redis->info()['redis_version'] ?? '';
        } catch (\Exception $exception) {
            $redis_version = $exception->getMessage();
        }

        return [
            'os' => PHP_OS,
            'php_sapi' => php_sapi_name(),
            'php_version' => PHP_VERSION,
            'swoole_version' => SWOOLE_VERSION,
            'mysql_version' => $mysql_version,
            'redis_version' => $redis_version,
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'max_execution_time' => ini_get('max_execution_time') . '秒',
            'free_space' => round((@disk_free_space(".") / (1024 * 1024)), 2) . 'M',
        ];
    }
}
