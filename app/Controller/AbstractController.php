<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/5/12 11:55
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Controller;

use App\Service\ApiService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class AbstractController
{
    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    #[Inject]
    protected RequestInterface $request;

    #[Inject]
    protected ResponseInterface $response;

    #[Inject]
    protected ApiService $api_service;

    /**
     * @param array  $data
     * @param string $msg
     * @param int    $code
     * @param bool   $status
     * @return PsrResponseInterface
     */
    protected function returnSuccessJson(
        array $data = [],
        string $msg = '',
        int $code = 200,
        bool $status = true
    ): PsrResponseInterface {
        !$msg && $msg = $this->request->isMethod('GET') ? '请求成功' : '操作成功';

        return $this->response->json($this->api_service->encryptData(compact('data', 'msg', 'status', 'code')));
    }

    protected function returnErrorJson(
        string $msg = '操作失败',
        int $code = 500,
        array $data = [],
        bool $status = false
    ): PsrResponseInterface {
        return $this->response->json($this->api_service->encryptData(compact('data', 'msg', 'status', 'code')));
    }

    protected function returnSuccessJsonWithoutEncrypt(
        array $data = [],
        string $msg = '',
        int $code = 200,
        bool $status = true
    ): PsrResponseInterface {
        !$msg && $msg = $this->request->isMethod('GET') ? '请求成功' : '操作成功';

        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }

    protected function returnErrorJsonWithoutEncrypt(
        string $msg = '操作失败',
        int $code = 500,
        array $data = [],
        bool $status = false
    ): PsrResponseInterface {
        return $this->response->json(compact('data', 'msg', 'status', 'code'));
    }
}