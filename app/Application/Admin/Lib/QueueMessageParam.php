<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/28 14:21
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Lib;

use Hyperf\AsyncQueue\AnnotationJob;
use Hyperf\AsyncQueue\MessageInterface;

class QueueMessageParam
{

    protected string $class_name = '';
    protected string $method = '';
    protected string $params = '[]';
    protected string $params_md5 = '';

    /**
     * @param MessageInterface $message
     */
    public function __construct(MessageInterface $message)
    {
        $job = $message->job();
        if ($job instanceof AnnotationJob) {
            //注解类型的job
            $class_name = $job->class ?? '';
            $method = $job->method ?? '';
            $params = $job->params ?? [];

        } else {
            $class_name = get_class($job);
            $method = 'handle';
            $params = get_object_vars($job);
        }

        $this->class_name = $class_name;
        $this->method = $method;
        $this->params = json_encode($params);
        $this->params_md5 = md5($class_name . $method . json_encode($params));
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->class_name;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return false|string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return false|string
     */
    public function getParamsMd5()
    {
        return $this->params_md5;
    }
}