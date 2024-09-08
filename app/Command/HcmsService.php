<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;
use function Hyperf\Config\config;

#[Command]
class HcmsService extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('Hcms:service');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module service file');
        //创建控制器命令
        $this->addArgument('module', InputArgument::REQUIRED, '执行的模块');
        $this->addArgument('service_name', InputArgument::REQUIRED, '创建 service 名称');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $service_name = $this->input->getArgument('service_name');
        //执行数据库部署
        try {
            if (!is_dir("app/Application/{$module_name}/Service/")) {
                mkdir("app/Application/{$module_name}/Service/", 0700, true);
            }
            $service_dir = "app/Application/{$module_name}/Service/" . ucfirst($service_name) . 'Service.php';
            if (file_exists($service_dir)) {
                $this->output->error("Service file exist");

                return;
            }
            $service_stub = file_get_contents(__DIR__ . '/stub/service.stub');
            //替换相依的字符
            $res = str_replace(['{Module}', '{module}', '{Service}'],
                [$module_name, strtolower($module_name), ucfirst($service_name)], $service_stub);
            file_put_contents($service_dir, $res);
            $this->output->success('create ' . $service_dir . ' success');
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
