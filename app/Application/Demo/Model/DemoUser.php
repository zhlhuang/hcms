<?php

declare (strict_types=1);

namespace App\Application\Demo\Model;

use App\Model\AbstractAuthModel;
use Hyperf\Support\Network;
use Qbhy\HyperfAuth\Authenticatable;

/**
 * @property int            $demo_user_id
 * @property string         $username
 * @property string         $password
 * @property string         $login_ip
 * @property string         $login_time
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class DemoUser extends AbstractAuthModel
{

    protected string $primaryKey = 'demo_user_id';
    /**
     * The table associated with the model.
     *
     * @var ?string
     */
    protected ?string $table = 'demo_user';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['username'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['demo_user_id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    static function loginEvent($username, $password)
    {
        $user = self::firstOrCreate(['username' => $username]);
        $user->password = md5($password);
        $user->login_ip = Network::ip();
        $user->login_time = date('Y-m-d H:i:s');
        $user->save();

        return $user->login();
    }

    function getLoginInfo(): ?Authenticatable
    {
        /**
         * @var self $user ;
         */
        $user = $this->getLoginUser();

        return $user->setVisible(['username', 'login_ip', 'login_time']);
    }
}