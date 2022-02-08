<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/29
 * Time: 19:47.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\AdminUser;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Qbhy\HyperfAuth\AuthManager;

class AdminUserService
{

    /**
     * @Inject()
     * @var AuthManager
     */
    protected $auth;

    /**
     * @var ?AdminUser
     */
    protected $admin_user;

    private function __construct() { }

    static function getInstance(): self
    {
        /**
         * @var AdminUserService
         */
        return Context::getOrSet(self::class, function () {
            return new self();
        });
    }


    /**
     * @return AdminUser|null
     */
    public function getAdminUser(): ?AdminUser
    {
        if (!$this->admin_user) {
            try {
                $this->admin_user = $this->auth->guard('session')
                    ->user();
            } catch (\Throwable $exception) {

            }
        }

        return $this->admin_user;
    }

    public function getAdminUserId(): int
    {
        return $this->getAdminUser()->admin_user_id ?: 0;
    }

    public function getAdminUserRoleId(): int
    {
        return $this->getAdminUser()->role_id ?: 0;
    }
}