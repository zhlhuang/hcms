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
use App\Application\Admin\Lib\ThinkEngine;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\View\RenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

#[Aspect]
class ViewAspect extends AbstractAspect
{
    public ?int $priority = 99;
    public array $annotations = [
        View::class
    ];

    #[Inject]
    protected ContainerInterface $container;
    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected RenderInterface $render;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $render_param = $proceedingJoinPoint->process();
        //如果返回的是Response 对象，直接返回
        if ($render_param instanceof ResponseInterface) {
            return $render_param;
        }
        //获取template名称，默认是控制的方法名称
        $template = $proceedingJoinPoint->getReflectMethod()->name;

        /**
         * @var ?View $view_annotation
         */
        $view_annotation = $proceedingJoinPoint->getAnnotationMetadata()->method['App\Annotation\View'] ?? null;
        //根据注解传参获取模板
        $view_annotation && $view_annotation->template && $template = $view_annotation->template;
        if (!($render_param instanceof RenderParam)) {
            $render_param = RenderParam::display($template, $render_param ?: [])
                ->setLayout($view_annotation->layout);
        }
        $template = $render_param->template ?: $template; //拿出指定的模板
        $data = $render_param->getData();
        $engine = $this->container->get(ThinkEngine::class);
        $engine->setLayout($render_param->layout);

        $class = $proceedingJoinPoint->getReflectMethod()->class;

        //解析模块和Controller的名称。
        preg_match_all("/App\\\Application\\\\(.+?)\\\\Controller\\\\(.+?)Controller/", $class, $res__);
        $module_name = $res__[1][0] ?? '';
        $controller = $res__[2][0] ?? '';
        $controller = str_replace("\\", "/", $controller);
        $controller = strtolower($controller); //控制器名称
        $module_name = ucfirst($module_name); //模块名称

        // 根据模块和Controller名称，解析出template名称
        $template = "{$module_name}/View/{$controller}/{$template}";

        return $this->render->render($template, $data);
    }
}