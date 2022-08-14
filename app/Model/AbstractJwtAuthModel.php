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
     *  这里因为使用了模型缓存，所以重构 AuthAbility
     */
    use AuthAbilityCache, Cacheable;

    protected string $guard_key = 'api_auth';

    /**
     * @Inject()
     */
    protected AuthManager $auth;


    /**
     * @return mixed
     */
    public function login()
    {
        return $this->auth->guard($this->guard_key)
            ->login($this);
    }

    /**
     * 获取登录授权对象
     *
     * @return Authenticatable
     */
    protected function getLoginUser(): Authenticatable
    {
        return $this->auth->guard($this->guard_key)
            ->user();
    }

    public function checkLogin(): bool
    {
        return $this->auth->guard($this->guard_key)
            ->check();
    }

    public function logout()
    {
        return $this->auth->guard($this->guard_key)
            ->logout();
    }
}