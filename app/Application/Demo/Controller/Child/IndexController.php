<?php

declare(strict_types=1);

namespace App\Application\Demo\Controller\Child;

use App\Annotation\View;
use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;

#[Controller("/demo/child/index")]
class IndexController extends AbstractController
{
    #[View]
    #[GetMapping("index")]
    public function index()
    {
    }
}
