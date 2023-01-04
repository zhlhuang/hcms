<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

#[Command]
class HcmsController extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('Hcms:controller');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module controller file');
        //创建控制器命令
        $this->addArgument('module', InputArgument::REQUIRED, '执行的模块');
        $this->addArgument('controller_name', InputArgument::REQUIRED, '创建controller名称');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $controller_name = $this->input->getArgument('controller_name');
        //执行数据库部署
        try {
            $controller_dir = "app/Application/{$module_name}/Controller/" . ucfirst($controller_name) . 'Controller.php';
            if (file_exists($controller_dir)) {
                $this->output->error("Controller file exist");

                return;
            }
            $controller_stub = file_get_contents(__DIR__ . '/stub/controller.stub');
            //替换相依的字符
            $res = str_replace(['{Module}', '{module}', '{Controller}', '{controller}'],
                [$module_name, strtolower($module_name), ucfirst($controller_name), $controller_name],
                $controller_stub);
            file_put_contents($controller_dir, $res);
            $this->output->success('create ' . $controller_dir . ' success');
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
