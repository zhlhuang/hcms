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

use Monolog\Level;

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => BASE_PATH . '/runtime/logs/hyperf.log',
                'level' => Level::Debug
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'dateFormat' => 'Y-m-d H:i:s',
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
    'request' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/request/request.log',
                'level' => Level::Debug,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\JsonFormatter::class,
            'constructor' => [
                'batchMode' => Monolog\Formatter\JsonFormatter::BATCH_MODE_NEWLINES,
                'appendNewline' => true,
            ],
        ],
    ],
    'error' => [
        'handler' => [
            'class' => Monolog\Handler\RotatingFileHandler::class,
            'constructor' => [
                'filename' => BASE_PATH . '/runtime/logs/error/error.log',
                'level' => Level::Debug,
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'batchMode' => Monolog\Formatter\JsonFormatter::BATCH_MODE_NEWLINES,
                'appendNewline' => true,
            ],
        ],
    ],
];
