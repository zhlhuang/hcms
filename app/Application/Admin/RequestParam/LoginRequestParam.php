<?php

declare(strict_types=1);

namespace App\Application\Admin\RequestParam;

use App\Annotation\RequestParam;
use App\Controller\RequestParam\BaseRequestParam;

#[RequestParam]
class LoginRequestParam extends BaseRequestParam
{
    protected array $rules = [
        'username' => 'required',
        'password' => 'required',
        'valid_code' => 'required',
    ];

    protected array $message = [
        'username.required' => '请输入用户名',
        'password.required' => '请输入密码',
        'valid_code.required' => '请输入验证码',
    ];

    private string $username = '';
    private string $password = '';
    private string $valid_code = '';
    private int $time = 0;
    private string $user_agent = '';



    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getValidCode(): string
    {
        return $this->valid_code;
    }
    public function getTime(): int
    {
        return $this->time;
    }
    public function getUserAgent(): string
    {
        return $this->user_agent;
    }
}
