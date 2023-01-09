<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    //如果不需要队列处理，可以注释下面代码
    \Hyperf\AsyncQueue\Process\ConsumerProcess::class,
    \Hyperf\Crontab\Process\CrontabDispatcherProcess::class,
];
