<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/3 16:56
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\RequestParam;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Str;

#[Aspect]
class RequestParamAspect extends AbstractAspect
{
    public array $annotations = [
        RequestParam::class,
    ];

    #[Inject]
    protected RequestInterface $request;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $method = $proceedingJoinPoint->getReflectMethod()
            ->getName();
        if (Str::startsWith($method, 'get')) {
            $get_key = substr_replace(Str::snake($method), '', 0, 4);
            $default = $proceedingJoinPoint->process();

            return $this->castAttribute(gettype($default), $this->request->input($get_key, $default));
        } else {
            return $proceedingJoinPoint->process();
        }
    }

    /**
     * @param       $type
     * @param mixed $value
     * @return mixed
     */
    private function castAttribute($type, $value)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                return (int)$value;
            case 'float':
            case 'double':
                return (double)$value;
            default:
                return $value;
        }
    }
}