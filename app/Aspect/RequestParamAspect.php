<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/3 16:56
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\RequestParam;
use App\Service\ApiService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Stringable\Str;

#[Aspect]
class RequestParamAspect extends AbstractAspect
{
    public array $annotations = [
        RequestParam::class,
    ];

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ApiService $apiService;

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $method = $proceedingJoinPoint->getReflectMethod()
            ->getName();
        if (Str::startsWith($method, 'get')) {
            $get_key = substr_replace(Str::snake($method), '', 0, 4);
            $default = $proceedingJoinPoint->process();
            //获取对象中输入数据 input_data
            $input_data = $proceedingJoinPoint->getInstance()
                ->returnInputData();

            //根据格式，返回参数
            return $this->castAttribute(gettype($default), $input_data[$get_key] ?? $default);
        } else {
            return $proceedingJoinPoint->process();
        }
    }

    /**
     * @param string $type
     * @param mixed  $value
     * @return mixed
     */
    private function castAttribute(string $type, mixed $value): mixed
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