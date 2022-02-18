<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Application\Admin\Service\AdminUserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Container\ContainerInterface;
use Qbhy\HyperfAuth\AuthManager;

abstract class AdminAbstractController
{
    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    /**
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    protected function getAdminUserId(): int
    {
        return AdminUserService::getInstance()
            ->getAdminUserId();
    }


    protected function returnSuccessJson(array $data = [], $msg = '', int $code = 200, $status = true)
    {
        !$msg && $msg = $this->request->isMethod('GET') ? '请求成功' : '操作成功';

        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }

    protected function returnErrorJson(string $msg = '操作失败', int $code = 500, array $data = [], bool $status = false)
    {
        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }
}
