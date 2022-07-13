<?php

declare(strict_types=1);

namespace App\Application\Demo\Controller\Child;

use App\Annotation\View;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

/**
 * @Controller(prefix="/demo/child/index")
 */
class IndexController extends AbstractController
{

    /**
     * @View()
     * @GetMapping(path="index")
     */
    public function index() { }
}
