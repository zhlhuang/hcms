<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/25
 * Time: 20:24.
 */

declare(strict_types=1);

namespace App\Application\Admin\Lib;

use Hyperf\Contract\ConfigInterface;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Hyperf\View\Engine\EngineInterface;
use Hyperf\View\Engine\NoneEngine;
use Hyperf\View\Exception\EngineNotFindException;
use Hyperf\View\Exception\RenderException;
use Hyperf\View\Mode;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Hyperf\Task\Task;
use Hyperf\Task\TaskExecutor;

class Render
{

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $engine;

    /**
     * @var string
     */
    protected $mode;

    /**
     * @var array
     */
    protected $config;

    public function __construct(ContainerInterface $container, ConfigInterface $config)
    {
        $engine = $config->get('view.engine', NoneEngine::class);
        if (!$container->has($engine)) {
            throw new EngineNotFindException("{$engine} engine is not found.");
        }

        $this->engine = $engine;
        $this->mode = $config->get('view.mode', Mode::TASK);
        $this->config = $config->get('view.config', []);
        $this->container = $container;
    }

    public function render(string $template, array $data = [], string $view_path = ''): ResponseInterface
    {
        return $this->response()
            ->withAddedHeader('content-type', $this->getContentType())
            ->withBody(new SwooleStream($this->getContents($template, $data, $view_path)));
    }

    public function getContents(string $template, array $data = [], string $view_path = ''): string
    {

        if ($view_path !== '') {
            $config = array_merge($this->config, [
                'view_path' => $view_path
            ]);
        } else {
            $config = $this->config;
        }
        try {
            switch ($this->mode) {
                case Mode::SYNC:
                    /** @var EngineInterface $engine */ $engine = $this->container->get($this->engine);
                    $result = $engine->render($template, $data, $config);
                    break;
                case Mode::TASK:
                default:
                    $executor = $this->container->get(TaskExecutor::class);
                    $result = $executor->execute(new Task([$this->engine, 'render'], [$template, $data, $config]));
                    break;
            }

            return $result;
        } catch (\Throwable $throwable) {
            throw new RenderException($throwable->getMessage(), $throwable->getCode(), $throwable);
        }
    }

    public function getContentType(): string
    {
        $charset = !empty($this->config['charset']) ? '; charset=' . $this->config['charset'] : '';

        return 'text/html' . $charset;
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}