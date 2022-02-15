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
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * @Controller(prefix="admin/queue")
 */
class QueueController
{
    /**
     * @View()
     * @GetMapping(path="index")
     */
    function index()
    {
        return RenderParam::display();
    }
}