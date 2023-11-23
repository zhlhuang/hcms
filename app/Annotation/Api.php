<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/18 09:47
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use \Attribute;

#[Attribute(Attribute::TARGET_ALL)]
class Api extends AbstractAnnotation
{
    /**
     * 兼容3.1报弃用
     *
     * @param ...$value
     */
    public function __construct(...$value)
    {
        $formattedValue = $this->formatParams($value);
        foreach ($formattedValue as $key => $val) {
            if (property_exists($this, $key)) {
                $this->{$key} = $val;
            }
        }
    }

    /**
     * 兼容3.1报弃用
     *
     * @param $value
     * @return array
     */
    protected function formatParams($value): array
    {
        if (isset($value[0])) {
            $value = $value[0];
        }
        if (!is_array($value)) {
            $value = ['value' => $value];
        }

        return $value;
    }
}