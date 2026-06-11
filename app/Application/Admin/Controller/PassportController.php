<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Model\AdminLoginRecord;
use App\Application\Admin\Model\AdminUser;
use App\Application\Admin\RequestParam\LoginRequestParam;
use App\Application\Admin\Service\AdminSettingService;
use App\Controller\AbstractController;
use App\Exception\ErrorException;
use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Session\Middleware\SessionMiddleware;
use Intervention\Image\ImageManagerStatic as Image;
use Psr\SimpleCache\CacheInterface;

#[Middlewares([SessionMiddleware::class])]
#[Controller("admin/passport")]
class PassportController extends AbstractController
{

    #[Inject]
    protected AdminSettingService $admin_setting;

    #[Inject]
    protected SessionInterface $session;

    #[Inject]
    protected CacheInterface $cache;



    #[View('login')]
    #[GetMapping("/admin/login/{sale_code}")]
    public function saleLogin(string $sale_code)
    {
        if ($this->admin_setting->getSafeLogin()) {
            if ($sale_code != $this->admin_setting->getSafeLoginCode()) {
                throw new ErrorException("安全登录码错误");
            }
        }
        $this->session->set('sale_code', $sale_code);
    }

    #[Api]
    #[RequestMapping("logout")]
    function logout()
    {
        $admin_user = (new AdminUser())->getLoginUserInfo();
        $admin_user->logout();

        return [];
    }

    #[Api]
    #[PostMapping("login")]
    function doLogin()
    {

        $login_request_param = new LoginRequestParam();
        $login_request_param->validatedThrowMessage();
        $username = $login_request_param->getUsername();
        //生成密码
        $password = $login_request_param->getPassword();
        $valid_code = $login_request_param->getValidCode();
        $time = $login_request_param->getTime();


        $login_record = new AdminLoginRecord();
        $login_record->username = $username;
        $login_record->login_result = AdminLoginRecord::LOGIN_RESULT_FAIL;
        $login_record->ip = getIp();
        $login_record->user_agent = substr($login_request_param->getUserAgent(), 0, 1000);

        try {
            $cache_code = $this->session->get('valid_' . $time);
            if ($valid_code != $cache_code) {
                $login_record->result_msg = '验证码错误';
                $login_record->save();

                return $this->returnErrorJson('验证码错误');
            }

            $login_fail_key = 'login_fail_count_' . md5($username);
            $login_lock_key = 'login_lock_until_' . md5($username);
            $login_lock_until = (int)$this->cache->get($login_lock_key, 0);
            if ($login_lock_until > time()) {
                $login_record->result_msg = '账号已锁定';
                $login_record->save();

                return $this->returnErrorJson('账号密码错误次数过多，请30分钟后再试');
            }

            $admin_user = AdminUser::where('username', $username)
                ->first();
            if ($admin_user instanceof AdminUser && $admin_user->passwordVerify($password)) {
                $this->cache->delete($login_fail_key);
                $this->cache->delete($login_lock_key);
                $login_record->login_result = AdminLoginRecord::LOGIN_RESULT_SUCCESS;
                $login_record->save();

                return $this->returnSuccessJson([
                    'status' => $admin_user->login(),
                ], '登录成功');
            } else {
                $login_fail_count = (int)$this->cache->get($login_fail_key, 0) + 1;
                $this->cache->set($login_fail_key, $login_fail_count, 1800);
                if ($login_fail_count >= 5) {
                    $this->cache->set($login_lock_key, time() + 1800, 1800);
                    $login_record->result_msg = '账号已锁定';
                    $login_record->save();

                    return $this->returnErrorJson('账号密码错误次数过多，请30分钟后再试');
                }

                $login_record->result_msg = '账号或密码错误';
                $login_record->save();

                return $this->returnErrorJson('账号或密码错误');
            }
        } catch (\Throwable $exception) {
            $login_record->result_msg = $exception->getMessage();
            $login_record->save();
            throw $exception;
        }
    }

    #[View]
    #[GetMapping("login")]
    public function login()
    {
        if ($this->admin_setting->getSafeLogin()) {
            $sale_code = $this->session->get('sale_code', '');
            if (empty($sale_code) || $sale_code != $this->admin_setting->getSafeLoginCode()) {
                throw new ErrorException("安全登录码错误");
            }
        }

        return [
            'site_name' => $this->admin_setting->getSiteSetting('site_name', 'Hcms')
        ];
    }


    /**
     * 验证码获取
     */
    #[GetMapping("code")]
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
        $this->session->set('valid_' . $time, $code);

        return $this->response->withHeader('Content-Type', 'image/png')
            ->raw($image->encode('png'));
    }
}
