<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/18 11:08
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Middleware\AdminMiddleware;
use Hyperf\Session\Middleware\SessionMiddleware;
use App\Controller\AbstractController;
use Hyperf\Cache\Collector\FileStorage;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\Redis\Redis;
use \Hyperf\Codec\Json;

#[Middlewares([SessionMiddleware::class,AdminMiddleware::class])]
#[Controller("admin/cache")]
class CacheController extends AbstractController
{

    const REDIS_HASH = 5;
    const REDIS_LIST = 3;
    #[Inject]
    protected ConfigInterface $config;
    #[Inject]
    protected Redis $redis;

    #[Api]
    #[PostMapping("flush")]
    public function flushCache()
    {
        $res = $this->redis->flushDB();

        return $res ? [] : $this->returnErrorJson();
    }


    /**
     * 获取缓存详情
     */
    #[Api]
    #[GetMapping("detail/{cache_key}")]
    function cacheDetail($cache_key = '')
    {
        //为了安全起见，会把文件名含有/的符号替换掉。
        $cache_key = str_replace(['../', './'], '', trim(urldecode($cache_key)));
        $cache_config = $this->config->get('cache.default');
        $is_redis_drive = $cache_config['driver'] == 'Hyperf\Cache\Driver\RedisDriver';
        if ($is_redis_drive) {
            if (!$this->redis->keys($cache_key)) {
                return $this->returnErrorJson('找不到该缓存记录');
            }
            $detail = $this->redis->get($cache_key);
            if ($this->redis->type($cache_key) == self::REDIS_HASH) {
                $detail = $this->redis->hGetAll($cache_key);
            }
            if ($this->redis->type($cache_key) == self::REDIS_LIST) {
                $detail = $this->redis->lRange($cache_key, 0, -1);
            }
        } else {
            //文件系统
            $file_path = BASE_PATH . '/runtime/caches/' . $cache_key;
            if (!file_exists($file_path)) {
                return $this->returnErrorJson('找不到该缓存文件');
            }
            $detail = file_get_contents($file_path);
        }

        try {
            $format_detail = unserialize($detail);
            if ($format_detail instanceof FileStorage) {
                $format_detail = $format_detail->getData();
            }
            $format_detail = Json::encode($format_detail);
        } catch (\Throwable $exception) {
            $format_detail = $detail;
        }

        return compact('detail', 'format_detail');
    }

    /**
     * 删除缓存
     */
    #[Api]
    #[DeleteMapping("{cache_key}")]
    function cacheDelete($cache_key = '')
    {
        //为了安全起见，会把文件名含有/的符号替换掉。
        $cache_key = str_replace(['../', './'], '', trim(urldecode($cache_key)));
        $cache_config = $this->config->get('cache.default');
        $is_redis_drive = $cache_config['driver'] == 'Hyperf\Cache\Driver\RedisDriver';
        if ($is_redis_drive) {
            if (!$this->redis->keys($cache_key)) {
                return $this->returnErrorJson('找不到该缓存记录');
            }

            return $this->redis->del($cache_key) > 0 ? $this->returnSuccessJson() : $this->returnErrorJson();
        } else {
            //文件系统
            $file_path = BASE_PATH . '/runtime/caches/' . $cache_key;
            if (!file_exists($file_path)) {
                return $this->returnErrorJson('找不到该缓存文件');
            }

            return unlink($file_path) ? $this->returnSuccessJson() : $this->returnErrorJson();
        }
    }

    /**
     * 获取当前缓存信息、数据
     */
    #[Api]
    #[GetMapping("info")]
    function info()
    {
        $cache_config = $this->config->get('cache.default');
        $is_redis_drive = $cache_config['driver'] == 'Hyperf\Cache\Driver\RedisDriver';
        try {
            $redis_info = $this->redis->info();
        } catch (\Exception $exception) {
            $redis_info = [];
        }
        $redis_status = empty($redis_info) ? 0 : 1;
        if ($redis_status == 1) {
            $redis_config = $this->config->get('redis.default');
            $redis_info = [
                'host' => $redis_config['host'] ?? '',
                'port' => $redis_config['port'] ?? '',
                'total_system_memory_human' => $redis_info['total_system_memory_human'] ?? '',
                'version' => $redis_info['redis_version'] ?? ''
            ];
        }
        $config_info = [
            'driver' => $is_redis_drive ? 'Redis' : '文件',
            'redis_status' => $redis_status, //redis的启动状态
            'redis_info' => $redis_info,//redis信息，没有为空
        ];
        $keys = [];
        if ($is_redis_drive) {
            $keys = $this->redis->keys('*');
        } else {
            //如果是文件存储
            $dir = BASE_PATH . '/runtime/caches/';
            if (!is_dir($dir)) {
                mkdir($dir, 0700, true);
            }
            $file_list = scandir($dir);
            foreach ($file_list as $file) {
                if ($file != '.' && $file != '..') {
                    $keys[] = $file;
                }
            }
        }
        sort($keys);

        return compact('config_info', 'keys');
    }

    #[View]
    #[GetMapping("")]
    function index()
    {
    }
}