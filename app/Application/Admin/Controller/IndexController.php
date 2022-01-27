<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Application\Admin\Model\AdminUser;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * @Controller(prefix="/admin/index")
 */
class IndexController extends AdminAbstractController
{
    /**
     * @GetMapping(path="index")
     */
    function index()
    {
        /**
         * @var AdminUser $admin_user
         */
        try {
            $admin_user = $this->auth->guard('session')
                ->user();
        } catch (\Exception $exception) {
            return $this->returnSuccessJson(['msg' => '用户没有登录']);
        }

        if (empty($admin_user->admin_user_id)) {
            return $this->returnSuccessJson(['msg' => '用户没有登录']);
        }

        return $this->returnSuccessJson(['msg' => '用户已经登录']);
    }
}
