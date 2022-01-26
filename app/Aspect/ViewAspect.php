<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/23
 * Time: 18:09.
 */

namespace App\Aspect;

use App\Admin\Lib\Render;
use App\Annotation\View;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Psr\Container\ContainerInterface;

/**
 * @Aspect()
 */
class ViewAspect extends AbstractAspect
{
    public $annotations = [
        View::class
    ];

    protected $container;
    protected $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $res = $proceedingJoinPoint->process();
        $class = $proceedingJoinPoint->getReflectMethod()->class;
        //解析调用Controller的命名空间。
        list($module_name, $controller) = (explode('\\Controller\\', str_replace(['App\\'], '', $class)));
        $controller = strtolower(str_replace("Controller", '', $controller)); //控制器名称
        $module_name = ucfirst($module_name); //模块名称

        // 根据模块和Controller名称，解析出template 目录
        $view_path = BASE_PATH . "/app/{$module_name}/View/{$controller}/";
        $render = new Render($this->container, $this->config);
        //获取template名称，默认是控制的方法名称
        $template = $proceedingJoinPoint->getReflectMethod()->name;

        return $render->render($template, $res, $view_path);
    }
}