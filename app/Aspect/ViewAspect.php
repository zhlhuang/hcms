<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/23
 * Time: 18:09.
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\View\RenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

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

    /**
     * @Inject()
     * @var RenderInterface
     */
    protected $render;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $this->container = $container;
        $this->config = $config;
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $res = $proceedingJoinPoint->process();
        //如果返回的是Response 对象，直接返回
        if ($res instanceof ResponseInterface) {
            return $res;
        }

        //获取template名称，默认是控制的方法名称
        $template = $proceedingJoinPoint->getReflectMethod()->name;
        if ($res instanceof RenderParam) {
            $template = $res->template ?: $template; //拿出指定的模板
            $data = $res->getData();
        } else {
            $data = $res;
        }
        $class = $proceedingJoinPoint->getReflectMethod()->class;
        //解析调用Controller的命名空间。
        list($module_name, $controller) = (explode('\\Controller\\', str_replace(['App\\Application\\'], '', $class)));
        $controller = strtolower(str_replace("Controller", '', $controller)); //控制器名称
        $module_name = ucfirst($module_name); //模块名称
        $controller = str_replace("\\", "/", $controller);

        // 根据模块和Controller名称，解析出template名称
        $template = "{$module_name}/View/{$controller}/" . $template;

        return $this->render->render($template, $data);
    }
}