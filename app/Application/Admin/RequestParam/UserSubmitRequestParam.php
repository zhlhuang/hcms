<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/4 10:29
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class UserSubmitRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'role_id' => 'required',
        'real_name' => 'required',
        'username' => 'required',
    ];

    protected array $message = [
        'role_id.required' => '请选择所属的角色',
        'real_name.required' => '请输入管理员姓名',
        'username.required' => '请输入登录用户名',
    ];

    private int $admin_user_id = 0;
    private string $password = '';
    private string $username = '';
    private string $real_name = '';
    private int $role_id = 0;

    /**
     * @return int
     */
    public function getAdminUserId(): int
    {
        return $this->admin_user_id;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getRealName(): string
    {
        return $this->real_name;
    }

    /**
     * @return int
     */
    public function getRoleId(): int
    {
        return $this->role_id;
    }
}