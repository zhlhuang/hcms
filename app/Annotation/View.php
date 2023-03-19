<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/23
 * Time: 17:17.
 */

namespace App\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use \Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class View extends AbstractAnnotation
{
    public string $template = '';
    public bool $layout = true;
}