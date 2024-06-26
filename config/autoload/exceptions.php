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
    //忽略错误日志的异常代码
    'ignore_log_code' => [401],
    'handler' => [
        'http' => [
            //处理路由解析问题
            App\Exception\Handler\HttpExceptionHandler::class,
            App\Exception\Handler\ErrorExceptionHandler::class,
            App\Exception\Handler\ApiErrorExceptionHandler::class,
            App\Exception\Handler\SqlExceptionHandler::class,
            App\Exception\Handler\AppExceptionHandler::class,
        ],
    ],
];
