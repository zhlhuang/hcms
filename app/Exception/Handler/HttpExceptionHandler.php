<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Exception\NotFoundException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Exception\HttpException;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Codec\Json;
use Hyperf\View\Exception\RenderException;
use Hyperf\View\RenderInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class HttpExceptionHandler extends ExceptionHandler
{
    #[Inject]
    protected ConfigInterface $config;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected RenderInterface $render;

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
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
        if ($xmlhttprequest === 'xmlhttprequest') {
            //如果是ajax 请求，返回json 错误
            $result = Json::encode([
                'status' => false,
                'code' => 404,
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
        return $throwable instanceof NotFoundException || $throwable instanceof HttpException || $throwable instanceof RenderException;
    }
}
