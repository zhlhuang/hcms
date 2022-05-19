<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/18 09:47
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Annotation\Api;
use App\Exception\ApiErrorException;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

/**
 * @Aspect()
 */
class ApiAspect extends AbstractAspect
{
    public $priority = 99;
    public $annotations = [
        Api::class
    ];

    /**
     * @throws ApiErrorException
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        try {
            return $proceedingJoinPoint->process();
        } catch (\Throwable $exception) {
            throw new ApiErrorException($exception->getMessage(), (int)$exception->getCode(),
                $exception->getPrevious());
        }
    }
}