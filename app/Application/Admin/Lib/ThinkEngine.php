<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/11 15:25
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Lib;

use Hyperf\View\Engine\EngineInterface;
use think\Template;

class ThinkEngine implements EngineInterface
{
    protected bool $layout = true;

    public function render($template, $data, $config, $layout = true): string
    {
        $engine = new Template($config);

        $engine->assign($data);
        $engine->layout($this->layout);

        return $engine->fetch($template);
    }

    /**
     * @param bool $layout
     */
    public function setLayout(bool $layout): void
    {
        $this->layout = $layout;
    }
}