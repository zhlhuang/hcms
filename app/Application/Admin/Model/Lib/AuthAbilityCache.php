<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/16 21:15
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Admin\Model\Lib;

use Hyperf\Database\Model\Model;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * Trait AuthAbility.
 *
 * @mixin Authenticatable|Model
 */
trait AuthAbilityCache
{
    public function getId()
    {
        return $this->getKey();
    }

    public static function retrieveById($key): ?Authenticatable
    {
        return self::findFromCache($key);
    }
}