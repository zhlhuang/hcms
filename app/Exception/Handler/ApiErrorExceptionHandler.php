<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ApiErrorException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\Codec\Json;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ApiErrorExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;

    /**
     * @Inject()
     */
    protected ConfigInterface $config;

    /**
     * @Inject()
     */
    protected RequestInterface $request;

    /**
     * @inject()
     */
    protected RenderInterface $render;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('Exception', 'error');
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $description = $throwable->getMessage();
        $app_env = $this->config->get('app_env', 'dev');
        $error_detail = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
        $content = $throwable->getTrace();
        if ($app_env === 'dev') {
            $location = $error_detail;
        } else {
            $location = '';
            $content = '';
        }
        //记录错误日志
        $this->logger->error($error_detail);
        $this->logger->error($throwable->getTraceAsString());
        $this->stopPropagation();
        //返回json 错误
        $result = Json::encode([
            'status' => false,
            'code' => $throwable->getCode(),
            'data' => compact('description', 'location', 'content'),
            'msg' => $throwable->getMessage()
        ]);

        return $response->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($result));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ApiErrorException;
    }
}
