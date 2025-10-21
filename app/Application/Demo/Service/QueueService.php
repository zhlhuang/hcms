<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 20:40
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Demo\Service;

use Hyperf\AsyncQueue\Annotation\AsyncQueueMessage;

class QueueService
{
    #[AsyncQueueMessage(delay: 5)]
    function setDelayMessage($data)
    {
        var_dump("开始执行 DelayMessage " . $data['id']);
        sleep(2);
        var_dump("执行 DelayMessage 完成");
    }

    #[AsyncQueueMessage]
    function setLongMessage($data)
    {
        var_dump("开始执行 LongMessage " . $data['id']);
        sleep(5);
        var_dump("执行 LongMessage 完成");
    }

    #[AsyncQueueMessage(maxAttempts: 1)]
    function setErrorMessage($data)
    {
        var_dump("开始执行 ErrorMessage " . $data['id']);
        sleep(2);
        throw new \Exception('throw ErrorMessage');
    }
}