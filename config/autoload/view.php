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

use Hyperf\View\Engine\NoneEngine;
use Hyperf\View\Mode;

return [
    'engine' => \Hyperf\View\Engine\ThinkEngine::class,
    'mode' => Mode::SYNC,
    'config' => [
        'view_suffix' => 'php',
        'view_path' => BASE_PATH . "/app/Application/",
        'cache_path' => BASE_PATH . '/runtime/view/',
        'layout_on' => true,
        'layout_name' => 'Admin/View/common/layout',
        'taglib_pre_load' => 'App\Application\Admin\View\HcmsTag',
        'display_cache' => (env('APP_ENV') !== 'dev'), //开发模式下，模板不缓存
        'tpl_cache' => (env('APP_ENV') !== 'dev') //开发模式下，模板不缓存
    ],
];
