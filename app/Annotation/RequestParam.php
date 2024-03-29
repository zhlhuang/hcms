<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/3 16:55
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use \Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class RequestParam extends AbstractAnnotation
{
}
