<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ApiErrorException;
use App\Service\ApiService;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use \Hyperf\Codec\Json;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ApiErrorExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;

    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected RenderInterface $render;

    #[Inject]
    protected ApiService $api_service;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('Exception', 'error');
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $error_id = uniqid("API_");
        $app_env = $this->config->get('app_env', 'dev');
        $error_detail = sprintf('%s %s[%s] in %s', $error_id, $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        $content = $throwable->getTrace();
        $message = $throwable->getMessage();
        if ($app_env === 'dev') {
            $location = $error_detail;
            $data = compact('error_id', 'location', 'content');
        } else {
            //如果是原来是PDOException
            if ($throwable->getPrevious() instanceof \PDOException) {
                $message = "系统数据错误:" . $error_id;
            }
            $data = [];
        }
        if (!in_array($throwable->getCode(), $this->config->get('exceptions.ignore_log_code'))) {
            //记录错误日志
            $this->logger->error($error_detail, $throwable->getTrace());
        }
        $this->stopPropagation();
        //返回json 错误
        $result = Json::encode($this->api_service->encryptData([
            'status' => false,
            'code' => $throwable->getCode(),
            'data' => $data,
            'msg' => $message
        ]));

        return $response->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($result));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ApiErrorException;
    }
}
