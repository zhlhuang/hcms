<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/26
 * Time: 11:56.
 */

declare(strict_types=1);

namespace App\Application\Admin\Middleware;

use App\Application\Admin\Model\AdminUser;
use App\Application\Admin\Service\AccessService;
use App\Application\Admin\Service\AdminSettingService;
use App\Exception\ApiErrorException;
use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Exception\NotFoundHttpException;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;

class AdminMiddleware implements MiddlewareInterface
{
    /**
     * @Inject()
     */
    protected AdminUser $amin_user;

    /**
     * @Inject()
     */
    protected HttpResponse $response;

    protected LoggerInterface $logger;

    /**
     * @Inject()
     */
    protected AdminSettingService $setting;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('admin', 'request');
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$this->amin_user->checkLogin()) {
            // ajax请求默认是返回json格式
            $xmlhttprequest_header = $request->getHeader('X-Requested-With');
            $xmlhttprequest = strtolower($xmlhttprequest_header[0] ?? '');
            if ($xmlhttprequest === 'xmlhttprequest') {
                //ajax 可以返回json格式
                throw new ApiErrorException('未登录', 501);
            }
            throw new ErrorException('未登录', 501);
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

        $response = $handler->handle($request);
        $this->log($request, $response);

        return $response;
    }

    /**
     * 记录post请求日志
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     */
    private function log(ServerRequestInterface $request, ResponseInterface $response)
    {
        if ($request->getMethod() === 'POST') {
            //只对ost请求进行记录
            $log_is_open = (int)$this->setting->getLogSetting('log_is_open', 1);
            if ($log_is_open === 1) {
                $response_body = json_decode($response->getBody()
                    ->getContents(), true);
                if (!is_array($response_body)) {
                    $response_body = $response->getBody()
                        ->getContents();
                }
                $this->logger->info('request ' . $request->getMethod() . ' ' . $request->getUri(), [
                    'query' => $request->getQueryParams(),
                    'body' => $request->getParsedBody(),
                    'response_code' => $response->getStatusCode(),
                    'response_body' => $response_body
                ]);
            }
        }
    }
}