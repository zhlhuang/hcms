<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/26
 * Time: 11:56.
 */

declare(strict_types=1);

namespace App\Application\Admin\Middleware;

use App\Application\Admin\Service\AccessService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Qbhy\HyperfAuth\AuthManager;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    /**
     * @var HttpResponse
     */
    protected $response;

    public function __construct(ContainerInterface $container, HttpResponse $response)
    {
        $this->container = $container;
        $this->response = $response;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //TODO 如果是其他调用方式，例如API的post请求、ajax 可以返回json格式
        if (!$this->auth->guard('session')
            ->check()) {
            return $this->response->redirect('/admin/passport/login');
        }
        /**
         * 获取当前访问的 path
         */
        $path = trim($request->getUri()
            ->getPath(), '/');
        /**
         * 校验权限
         */
        if (!AccessService::getInstance()
            ->checkAccess($path)) {
            throw new NotFoundHttpException('非法请求');
        }

        return $handler->handle($request);
    }
}