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
        'retry_seconds' => [5, 300, 600, 3600, 10800, 18000, 86400],//5秒、5分钟、10分钟、1小时、3小时,5小时,24小时
        'handle_timeout' => 10,
        'processes' => 1,
        'concurrent' => [
            'limit' => 10,
        ],
    ],
];