<?php

declare(strict_types=1);

namespace App\Admin\Controller;

use App\Annotation\View;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\View\RenderInterface;

/**
 * @AutoController(prefix="/admin/passport")
 */
class PassportController
{

    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    /**
     * @View()
     * @GetMapping()
     */
    public function login()
    {
        return ['title' => 'This is login page'];
    }

    /**
     * @View()
     * @GetMapping(path="index")
     */
    public function index()
    {
        return [
            'ok' => $this->request->input('ok')
        ];
    }
}
