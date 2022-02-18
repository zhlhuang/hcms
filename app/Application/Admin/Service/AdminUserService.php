<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/29
 * Time: 19:47.
 */

declare(strict_types=1);

namespace App\Application\Admin\Service;

use App\Application\Admin\Model\AdminRole;
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

    /**
     * 获取所有下级角色id
     *
     * @param int $parent_role_id
     * @param int $level
     * @return array
     */
    public function getAdminUserChildRoleIds(int $parent_role_id = 0, int $level = 1): array
    {
        if ($parent_role_id == 0) {
            $parent_role_id = $this->getAdminUserRoleId();
        }
        $role_ids = AdminRole::where('parent_role_id', $parent_role_id)
            ->pluck('role_id')
            ->toArray();
        if ($level > 3 || empty($role_ids)) {
            return [];
        }
        $child_role_ids = [];
        foreach ($role_ids as $role_id) {
            $child_role_ids = $this->getAdminUserChildRoleIds($role_id, $level++);
        }

        return array_merge($role_ids, $child_role_ids);
    }
}