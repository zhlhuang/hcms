<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/8/13 11:58
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Model;

use App\Application\Admin\Model\Lib\AuthAbilityCache;
use Hyperf\Database\Model\Model;
use Hyperf\Di\Annotation\Inject;
use Hyperf\ModelCache\Cacheable;
use Hyperf\ModelCache\CacheableInterface;
use Qbhy\HyperfAuth\Authenticatable;
use Qbhy\HyperfAuth\AuthManager;

abstract class AbstractJwtAuthModel extends Model implements Authenticatable, CacheableInterface
{
    /**
     * @Inject()
     */
    protected AuthManager $auth;

    use AuthAbilityCache, Cacheable;

    /**
     * 登录用户，获取登录token
     *
     * @param Authenticatable $user
     * @return mixed
     */
    protected function loginAuth(Authenticatable $user)
    {
        return $this->auth->guard('api_auth')
            ->login($user);
    }

    /**
     * 获取登录授权对象
     *
     * @return Authenticatable
     */
    protected function getLoginUser(): Authenticatable
    {
        return $this->auth->guard('api_auth')
            ->user();
    }
}