<?php
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
declare(strict_types=1);

namespace App\Exception\Handler;

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Logger\LoggerFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class AppExceptionHandler extends ExceptionHandler
{
    protected LoggerInterface $logger;
    /**
     * @Inject()
     */
    protected StdoutLoggerInterface $stdout_loger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('AppException', 'error');
    }

    public function handle(Throwable $throwable, ResponseInterface $response): ResponseInterface
    {
        $this->logger->error(sprintf('AppExceptionHandler :: %s %s[%s] in %s', get_class($throwable),
            $throwable->getMessage(), $throwable->getLine(), $throwable->getTraceAsString()));
        $this->logger->error($throwable->getTraceAsString());

        $this->stdout_loger->error(sprintf('AppExceptionHandler :: %s %s[%s] in %s', get_class($throwable),
            $throwable->getMessage(), $throwable->getLine(), $throwable->getTraceAsString()));
        $this->stdout_loger->error($throwable->getTraceAsString());

        return $response->withHeader('Server', 'Hyperf')
            ->withStatus(500)
            ->withBody(new SwooleStream('Internal Server Error.'));
    }

    public function isValid(Throwable $throwable): bool
    {
        return true;
    }
}
