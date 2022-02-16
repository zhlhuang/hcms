<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/15 09:19
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

return [
    'default' => [
        'driver' => Hyperf\AsyncQueue\Driver\RedisDriver::class,
        'redis' => [
            'pool' => 'default'
        ],
        'channel' => 'queue',
        'timeout' => 2,
        'retry_seconds' => [1, 5, 10, 20],
        'handle_timeout' => 10,
        'processes' => 1,
        'concurrent' => [
            'limit' => 10,
        ],
    ],
];