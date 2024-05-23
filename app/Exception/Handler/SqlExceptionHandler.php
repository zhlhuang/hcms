<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Service\ApiService;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use \Hyperf\Codec\Json;
use Hyperf\Logger\LoggerFactory;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use \PDOException;
use Psr\Log\LoggerInterface;
use Throwable;

class SqlExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected RenderInterface $render;

    #[Inject]
    protected ApiService $api_service;

    protected LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('Exception', 'error');
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $description = $throwable->getMessage();
        $error_detail = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        $app_env = $this->config->get('app_env', 'dev');
        if ($app_env === 'dev') {
            $location = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
            $content = $throwable->getTraceAsString();
        } else {
            $location = '';
            $content = '';
            //正式环境隐藏sql环境报错信息
            $description = "系统错误";
        }
        if (!in_array($throwable->getCode(), $this->config->get('exceptions.ignore_log_code'))) {
            //记录错误日志
            $this->logger->error($error_detail, $throwable->getTrace());
        }
        $this->stopPropagation();
        $xmlhttprequest = strtolower($this->request->header('X-Requested-With') ?: '');
        if ($xmlhttprequest === 'xmlhttprequest') {
            //如果是ajax 请求，返回json 错误
            $result = Json::encode($this->api_service->encryptData([
                'status' => false,
                'code' => $throwable->getCode(),
                'data' => compact('description', 'location', 'content'),
                'msg' => $description
            ]));

            return $response->withAddedHeader('content-type', 'application/json; charset=utf-8')
                ->withBody(new SwooleStream($result));
        }

        return $this->render->render('Admin/View/error', compact('description', 'location', 'content'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof PDOException;
    }
}
