<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/14 17:27
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;

/**
 * 指定到模块的静态文件，为了方便安装的模块可以自带静态文件
 */
#[Controller("assets")]
class AssetsController
{
    /**
     * @GetMapping(path="{module}/[{file_name:.+}]")
     */
    function moduleAssets(ResponseInterface $response, $module, $file_name = '')
    {
        //为了安全起见，会把文件名含有../的符号替换掉。
        $file_name = str_replace('../', '', trim($file_name));
        $module = ucfirst($module);
        $file_path = BASE_PATH . "/app/Application/{$module}/View/assets/" . $file_name;
        if (!file_exists($file_path)) {
            throw new NotFoundHttpException();
        }

        return $response->download($file_path);
    }
}