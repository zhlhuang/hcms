<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/9 17:10
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Aspect;

use App\Application\Admin\Model\CronLog;
use App\Exception\ErrorException;
use Hyperf\Crontab\Annotation\Crontab;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;


#[Aspect]
class CrontabAspect extends AbstractAspect
{
    public array $annotations = [
        Crontab::class
    ];

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $meta = $proceedingJoinPoint->getAnnotationMetadata();
        $annotation = $meta->class['Hyperf\Crontab\Annotation\Crontab'] ?? [];
        if ($annotation instanceof Crontab) {
            $cron_name = $annotation->name;
            $cron_rule = $annotation->rule;
            $cron_memo = $annotation->memo;
            $task_class = get_class($proceedingJoinPoint->getInstance());
            $cron = CronLog::create(compact('cron_memo', 'cron_name', 'cron_rule', 'task_class'));
            $time = time();
            try {
                $res = $proceedingJoinPoint->process();
                // var_dump($cron->toArray());
                $cron->result = CronLog::RESULT_SUCCESS;
                $cron->result_msg = 'ok';
                $cron->execute_time = time() - $time;
                $cron->save();

                return $res;
            } catch (\Throwable $exception) {
                $cron->result = CronLog::RESULT_FAILED;
                $cron->result_msg = substr($exception->getMessage(), 0, 500);
                $cron->execute_time = time() - $time;
                $cron->save();
                throw new ErrorException($exception->getMessage());
            }
        } else {
            return $proceedingJoinPoint->process();
        }
    }

}