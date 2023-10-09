<?php

declare(strict_types=1);

namespace App\Application\Admin\Controller;

use App\Annotation\Api;
use App\Annotation\View;
use App\Application\Admin\Model\AdminLoginRecord;
use App\Application\Admin\Model\AdminUser;
use App\Application\Admin\Service\AdminSettingService;
use App\Controller\AbstractController;
use Hyperf\Contract\SessionInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Intervention\Image\ImageManagerStatic as Image;

#[Controller("admin/passport")]
class PassportController extends AbstractController
{

    #[Inject]
    protected AdminSettingService $admin_setting;

    #[Inject]
    protected SessionInterface $session;

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
        $username = $this->request->post('username', '');
        //生成密码
        $password = AdminUser::makePassword($username, $this->request->post('password', ''));
        $valid_code = $this->request->post('valid_code', '');
        $time = $this->request->post('time', 0);
        $login_record = new AdminLoginRecord();
        $login_record->username = $username;
        $login_record->login_result = AdminLoginRecord::LOGIN_RESULT_FAIL;
        $login_record->ip = getIp();
        $login_record->user_agent = substr($this->request->input('user_agent', ''), 0, 1000);

        try {
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
                $login_record->result_msg = $validator->errors()
                    ->first();
                $login_record->save();

                return $this->returnErrorJson($validator->errors()
                    ->first());
            }
            $cache_code = $this->session->get('valid_' . $time);
            if ($valid_code != $cache_code) {
                $login_record->result_msg = '验证码错误';
                $login_record->save();

                return $this->returnErrorJson('验证码错误');
            }

            /** @var ?AdminUser $admin_user */
            $admin_user = AdminUser::where(compact('username', 'password'))
                ->first();

            if (!empty($admin_user->admin_user_id)) {
                $login_record->login_result = AdminLoginRecord::LOGIN_RESULT_SUCCESS;
                $login_record->save();

                return $this->returnSuccessJson([
                    'status' => $admin_user->login(),
                ], '登录成功');
            } else {
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
