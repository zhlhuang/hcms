<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ErrorException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\Logger\LoggerFactory;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class ErrorExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;

    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected HttpResponse $http_response;

    #[Inject]
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
        if (!in_array($throwable->getCode(), $this->config->get('exceptions.ignore_log_code'))) {
            //记录错误日志
            $this->logger->error($error_detail, $throwable->getTrace());
        }
        $this->stopPropagation();
        if ($throwable->getCode() === 401) {
            return $this->http_response->redirect('/admin/passport/login');
        }

        return $this->render->render('Admin/View/error', compact('description', 'location', 'content'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ErrorException;
    }
}
