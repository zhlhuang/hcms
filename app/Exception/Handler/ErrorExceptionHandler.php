<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Exception\ErrorException;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Codec\Json;
use Hyperf\View\RenderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class ErrorExceptionHandler extends ExceptionHandler
{
    /**
     * @var StdoutLoggerInterface
     */
    protected $logger;

    /**
     * @Inject()
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var ConfigInterface
     */
    protected $config;

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @inject()
     * @var RenderInterface
     */
    protected $render;

    public function __construct(StdoutLoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        $description = $throwable->getMessage();
        $app_env = $this->config->get('app_env', 'dev');
        if ($app_env === 'dev') {
            $location = sprintf('%s[%s] in %s', $throwable->getMessage(), $throwable->getLine(), $throwable->getFile());
            $content = $throwable->getTraceAsString();
        } else {
            $location = '';
            $content = '';
        }
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