<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\View;
use App\Application\Admin\Lib\RenderParam;
use App\Application\Admin\Model\AdminUser;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\SimpleCache\CacheInterface;

/**
 * @Controller(prefix="admin/passport")
 */
class PassportController extends AdminAbstractController
{


    /**
     * @Inject()
     * @var CacheInterface
     */
    protected $cache;

    /**
     * @RequestMapping(path="logout")
     */
    function logout()
    {
        $this->auth->guard('session')
            ->logout();

        return $this->returnSuccessJson();
    }

    /**
     * @PostMapping(path="login")
     */
    function doLogin()
    {
        $username = $this->request->post('username', '');
        //生成密码
        $password = md5($this->request->post('password', '') . $username);
        $valid_code = $this->request->post('valid_code', '');
        $time = $this->request->post('time', 0);

        $validator = $this->validationFactory->make($this->request->all(), [
            'username' => 'required',
            'password' => 'required',
            'valid_code' => 'required',
        ], [
            'username.required' => '请输入用户名',
            'password.required' => '请输入登录密码',
            'valid_code.required' => '请输入验证码',
        ]);

        if ($validator->fails()) {
            return $this->returnSuccessError($validator->errors()
                ->first());
        }
        $cache_code = $this->cache->get('valid_' . $time);
        if ($valid_code != $cache_code) {
            return $this->returnSuccessError('验证码错误');
        }

        /** @var ?AdminUser $admin_user */
        $admin_user = AdminUser::where(compact('username', 'password'))
            ->first();

        if (!empty($admin_user->admin_user_id)) {
            return $this->returnSuccessJson([
                'status' => $this->auth->guard('session')
                    ->login($admin_user),
            ], '登录成功');
        } else {
            return $this->returnSuccessError('账号或密码错误');
        }
    }

    /**
     * @View()
     * @GetMapping(path="login")
     */
    public function login()
    {
        return RenderParam::display();
    }


    /**
     * 验证码获取
     * @GetMapping(path="code")
     */
    function code()
    {
        $time = $this->request->input('time', 0);
        $image = Image::canvas(160, 60, '#F5F7FA');
        $code = rand(1000, 9999) . '';
        $image->text($code, 25, 30, function ($font) {
            $font->file(BASE_PATH . '/public/assets/fonts/' . 'st-heiti-light.ttc');
            $font->size(48);
            $font->color('#409EFF');
            $font->align('left');
            $font->valign('center');
        });
        $this->cache->set('valid_' . $time, $code, 3600);

        return $this->response->withHeader('Content-Type', 'image/png')
            ->raw($image->encode('png'));
    }
}
