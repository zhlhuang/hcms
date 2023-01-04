<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/4 10:07
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Controller\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

/**
 * @RequestParam()
 */
class RoleSubmitRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'role_name' => 'required',
        'access_list' => 'required',
    ];

    protected array $message = [
        'role_name.required' => '请输入角色名称',
        'access_list.required' => '权限分配不能为空',
    ];

    private int $role_id = 0;
    private array $access_list = [];
    private int $parent_role_id = 0;
    private string $role_name = '';
    private string $description = '';

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }

    /**
     * @return array
     */
    public function getAccessList(): array
    {
        return $this->access_list;
    }

    /**
     * @return int
     */
    public function getParentRoleId(): int
    {
        return $this->parent_role_id;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->role_name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}