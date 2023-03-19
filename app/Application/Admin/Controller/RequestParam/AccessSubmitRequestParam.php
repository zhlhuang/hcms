<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/3 15:30
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller\RequestParam;

use App\Annotation\RequestParam;
use App\Application\Admin\Model\Access;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class AccessSubmitRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'access_name' => 'required',
        'uri' => 'required',
    ];

    protected array $message = [
        'access_name.required' => '请输入菜单/权限名称',
        'uri.required' => '请输入uri',
    ];

    private int $access_id = 0;
    private int $parent_access_id = 0;
    private string $access_name = '';
    private string $uri = '';
    private string $params = '';
    private int $sort = 100;
    private int $is_menu = Access::IS_MENU_YES;
    private string $menu_icon = '';

    /**
     * @return int
     */
    public function getAccessId(): int
    {
        return $this->access_id;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @return string
     */
    public function getParams(): string
    {
        return $this->params;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getIsMenu(): int
    {
        return $this->is_menu;
    }

    /**
     * @return string
     */
    public function getMenuIcon(): string
    {
        return $this->menu_icon;
    }


    /**
     * @return int
     */
    public function getParentAccessId(): int
    {
        return $this->parent_access_id;
    }

    /**
     * @return string
     */
    public function getAccessName(): string
    {
        return $this->access_name;
    }
}