<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/25
 * Time: 20:24.
 */

declare(strict_types=1);

namespace App\Application\Admin\Lib;

use App\Application\Admin\Service\AdminUserService;

class RenderParam
{
    public $template = '';
    protected $data = [];
    protected $common_data = [];


    public function __construct(array $data = [])
    {
        $this->data = $data;
        $admin_user = AdminUserService::getInstance()
            ->getAdminUser();
        $this->common_data = [
            'version' => "0.1.0",
            'admin_user' => $admin_user ? $admin_user->toArray() : []
        ];
    }

    public static function display(string $template = '', array $data = []): RenderParam
    {
        return (new self($data))->setTemplate($template);
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return array_merge($this->common_data, $this->data);
    }

    /**
     * @param array $common_data
     */
    public function setCommonData(array $common_data): void
    {
        $this->common_data = $common_data;
    }
}