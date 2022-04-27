<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/4/27 13:37
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use Hyperf\Logger\LoggerFactory;
use Psr\Log\LoggerInterface;

class ErrorLogService
{
    private LoggerInterface $logger;

    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->logger = $loggerFactory->get('Exception', 'error');
    }

    /**
     * 记录错误日志
     *
     * @param       $message
     * @param array $content
     */
    public function error($message, array $content = [])
    {
        $this->logger->error($message, $content);
    }
}