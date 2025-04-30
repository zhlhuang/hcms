<?php

namespace App\Application\Admin\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class UserResetRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'password' => 'required',
        'new_password' => 'required',
        'new_confirm_password' => 'required',
    ];

    protected array $message = [
        'password.required' => '请输入原密码',
        'new_password.required' => '请输入新密码',
        'new_confirm_password.required' => '请输入确认新密码',
    ];

    private string $password = '';
    private string $new_password = '';
    private string $new_confirm_password = '';

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getNewPassword(): string
    {
        return $this->new_password;
    }

    public function getNewConfirmPassword(): string
    {
        return $this->new_confirm_password;
    }
}