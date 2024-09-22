<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2023/1/3 18:10
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Controller\RequestParam;

use App\Exception\ErrorException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

abstract class BaseRequestParam
{
    protected array $rules = [];
    protected array $message = [];

    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    #[Inject]
    protected RequestInterface $request;

    protected array $input_data = [];


    public function returnInputData(): array
    {
        return $this->input_data;
    }

    public function __construct()
    {
        $this->input_data = $this->request->all();
    }

    /**
     * @return void
     * @throws ErrorException
     */
    public function validatedThrowMessage(): void
    {

        $validator = $this->validationFactory->make($this->input_data, $this->rules, $this->message);

        if ($validator->fails()) {
            throw new ErrorException($validator->getMessageBag()
                ->first());
        }
    }
}