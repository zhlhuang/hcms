#!/usr/bin/env php
<?php

ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');
ini_set('memory_limit', '1G');

error_reporting(E_ALL);

! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
// 使用基于原生 curl 的 NATIVE_CURL Hook 替代旧版 SWOOLE_HOOK_CURL，
// 旧 Hook 不支持 CURLOPT_HTTP_VERSION(HTTP/2)、CURLOPT_SHARE 等选项，
// 会导致 Symfony CurlHttpClient（EasyWeChat 依赖）抛 TransportException
! defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', (SWOOLE_HOOK_ALL & ~SWOOLE_HOOK_CURL) | SWOOLE_HOOK_NATIVE_CURL);

require BASE_PATH . '/vendor/autoload.php';

// Self-called anonymous function that creates its own scope and keep the global namespace clean.
(function () {
    Hyperf\Di\ClassLoader::init();
    /** @var Psr\Container\ContainerInterface $container */
    $container = require BASE_PATH . '/config/container.php';

    $application = $container->get(Hyperf\Contract\ApplicationInterface::class);
    $application->run();
})();
