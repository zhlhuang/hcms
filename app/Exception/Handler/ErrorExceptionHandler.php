<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ErrorException;
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

class ErrorExceptionHandler extends ExceptionHandler
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
        $content = $throwable->getTraceAsString();
        if ($app_env === 'dev') {
            $location = $error_detail;
        } else {
            $location = '';
            $content = '';
        }
        //记录错误日志
        $this->logger->error($error_detail);
        $this->logger->error($throwable->getTraceAsString());

        $xmlhttprequest = strtolower($this->request->header('X-Requested-With') ?: '');
        $this->stopPropagation();
        if ($xmlhttprequest === 'xmlhttprequest' || $this->request->isMethod('POST')) {
            //如果是ajax 请求，返回json 错误
            $result = Json::encode([
                'status' => false,
                'code' => $throwable->getCode(),
                'data' => compact('description', 'location', 'content'),
                'msg' => $throwable->getMessage()
            ]);

            return $response->withAddedHeader('content-type', 'application/json; charset=utf-8')
                ->withBody(new SwooleStream($result));
        }

        return $this->render->render('Admin/View/error', compact('description', 'location', 'content'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ErrorException;
    }
}
