<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/9 17:06
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Demo\Task;

use App\Exception\ErrorException;
use Hyperf\Crontab\Annotation\Crontab;

#[Crontab(rule: "*\/5 * * * * *", name: "demo", callback: "execute", memo: "这是一个定时任务案例", enable: false)]
class DemoTask
{
    public function execute(): bool
    {
        var_dump("Demo task execute " . date("Y-m-d H:i:s"));
        sleep(3);//执行时间模拟

        if (rand(1, 9) > 5) {
            throw new ErrorException('这是执行异常模拟');
        }

        return true;
    }
}