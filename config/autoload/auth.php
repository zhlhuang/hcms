<?php

declare(strict_types=1);

/**
 * This file is part of qbhy/hyperf-auth.
 *
 * @link     https://github.com/qbhy/hyperf-auth
 * @document https://github.com/qbhy/hyperf-auth/blob/master/README.md
 * @contact  qbhy0715@qq.com
 * @license  https://github.com/qbhy/hyperf-auth/blob/master/LICENSE
 */

use Qbhy\SimpleJwt\Encoders;
use Qbhy\SimpleJwt\EncryptAdapters as Encrypter;
use function Hyperf\Support\env;
use function Hyperf\Support\make;

return [
    'default' => [
        'guard' => 'session',
        'provider' => 'admin',
    ],
    'guards' => [
        'session' => [
            'driver' => Qbhy\HyperfAuth\Guard\SessionGuard::class,
            'provider' => 'admin',
        ],
        'api_auth' => [
            'driver' => Qbhy\HyperfAuth\Guard\JwtGuard::class,
            'provider' => 'api_auth',
            //TODO 具体的 JWT_SECRET 可以根据项目修改
            'secret' => env('JWT_SECRET', 'qbhy/hyperf-auth'),
            'ttl' => 60 * 60, // 登录有效期 单位秒
            'default' => Encrypter\PasswordHashEncrypter::class,
            'encoder' => new Encoders\Base64UrlSafeEncoder(),
            'cache' => function () {
                return make(Qbhy\HyperfAuth\HyperfRedisCache::class);
            },
        ],
    ],
    'providers' => [
        'api_auth' => [
            'driver' => \Qbhy\HyperfAuth\Provider\EloquentProvider::class,
            //TODO 根据具体的业务需求确定登录 Model
            'model' => \App\Application\Demo\Model\DemoUser::class
        ],
        'admin' => [
            'driver' => \Qbhy\HyperfAuth\Provider\EloquentProvider::class,
            'model' => \App\Application\Admin\Model\AdminUser::class
        ],
    ],
];
