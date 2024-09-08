<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;
use function Hyperf\Config\config;

#[Command]
class HcmsRequestParam extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('Hcms:rq');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module RequestParam file');
        //创建控制器命令
        $this->addArgument('module', InputArgument::REQUIRED, '执行的模块');
        $this->addArgument('request_param_name', InputArgument::REQUIRED, '创建 RequestParam 名称');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $request_param_name = $this->input->getArgument('request_param_name');
        //执行数据库部署
        try {
            if (!is_dir("app/Application/{$module_name}/RequestParam/")) {
                mkdir("app/Application/{$module_name}/RequestParam/", 0700, true);
            }
            $request_param_dir = "app/Application/{$module_name}/RequestParam/" . ucfirst($request_param_name) . 'RequestParam.php';
            if (file_exists($request_param_dir)) {
                $this->output->error("RequestParam file exist");

                return;
            }
            $request_param_stub = file_get_contents(__DIR__ . '/stub/request_param.stub');
            //替换相依的字符
            $res = str_replace(['{Module}', '{module}', '{RequestParam}'],
                [$module_name, strtolower($module_name), ucfirst($request_param_name)], $request_param_stub);
            file_put_contents($request_param_dir, $res);
            $this->output->success('create ' . $request_param_dir . ' success');
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
