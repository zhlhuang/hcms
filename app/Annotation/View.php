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