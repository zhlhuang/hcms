<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/13
 * Time: 18:49.
 */

declare(strict_types=1);

namespace App\Exception;

/**
 * 业务代码错误处理。
 */
class ErrorException extends \Exception
{
    public function __construct($message = "", $code = 500, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}