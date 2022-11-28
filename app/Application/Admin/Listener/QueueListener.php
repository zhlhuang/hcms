<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/16 11:09
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Listener;

use App\Application\Admin\Lib\QueueMessageParam;
use App\Application\Admin\Model\QueueList;
use Hyperf\AsyncQueue\Event\AfterHandle;
use Hyperf\AsyncQueue\Event\BeforeHandle;
use Hyperf\AsyncQueue\Event\Event;
use Hyperf\AsyncQueue\Event\FailedHandle;
use Hyperf\AsyncQueue\Event\RetryHandle;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener()
 */
class QueueListener implements ListenerInterface
{

    public function listen(): array
    {
        return [BeforeHandle::class, AfterHandle::class, FailedHandle::class, RetryHandle::class];
    }

    /**
     * 对接处理结果事件处理
     *
     * @param object $event
     */
    public function process(object $event): void
    {
        /**
         * @var Event $event
         */
        $message_param = new QueueMessageParam($event->getMessage());
        $class_name = $message_param->getClassName();
        $method = $message_param->getMethod();
        $params = $message_param->getParams();
        $params_md5 = $message_param->getParamsMd5();

        $queue_list = QueueList::firstOrCreate([
            'class_name' => $class_name,
            'method' => $method,
            'params_md5' => $params_md5
        ]);
        if ($event instanceof BeforeHandle) {
            //消息执行之前
            $queue_list->params = $params;
            $queue_list->status = QueueList::STATUS_PENDING;
        }
        if ($event instanceof AfterHandle) {
            //消息执行之前之后
            $queue_list->status = QueueList::STATUS_SUCCESS;
            $queue_list->process_time = time() - $queue_list->created_at->timestamp;
        }
        if ($event instanceof FailedHandle) {
            //消息执行失败
            $queue_list->status = QueueList::STATUS_FAILED;
            $queue_list->error_msg = $event->getThrowable()
                ->getMessage();
            $queue_list->error_data = substr($event->getThrowable()
                ->getTraceAsString(), 0, 2000);
        }
        if ($event instanceof RetryHandle) {
            $queue_list->process_count = $queue_list->process_count + 1;
            $queue_list->error_msg = $event->getThrowable()
                ->getMessage();
            $queue_list->error_data = substr($event->getThrowable()
                ->getTraceAsString(), 0, 2000);
        }
        $queue_list->save();
    }

}