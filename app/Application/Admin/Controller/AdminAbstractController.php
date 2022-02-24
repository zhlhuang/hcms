<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;
use Qbhy\HyperfAuth\AuthManager;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

abstract class AdminAbstractController
{
    /**
     * @Inject()
     */
    protected AuthManager $auth;

    /**
     * @Inject()
     */
    protected ValidatorFactoryInterface $validationFactory;
    /**
     * @Inject
     */
    protected ContainerInterface $container;

    /**
     * @Inject
     */
    protected RequestInterface $request;

    /**
     * @Inject
     */
    protected ResponseInterface $response;

    /**
     * @param array  $data
     * @param string $msg
     * @param int    $code
     * @param bool   $status
     * @return PsrResponseInterface
     */
    protected function returnSuccessJson(
        array $data = [],
        string $msg = '',
        int $code = 200,
        bool $status = true
    ): PsrResponseInterface {
        !$msg && $msg = $this->request->isMethod('GET') ? '请求成功' : '操作成功';

        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }

    protected function returnErrorJson(
        string $msg = '操作失败',
        int $code = 500,
        array $data = [],
        bool $status = false
    ): PsrResponseInterface {
        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }
}
