<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Config\config;

#[Command]
class HcmsView extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('Hcms:view');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module View file');
        //创建控制器命令
        $this->addArgument('module', InputArgument::REQUIRED, '执行的模块');
        $this->addArgument('controller', InputArgument::REQUIRED, '所属控制器');
        $this->addArgument('view_name', InputArgument::REQUIRED, '创建 View 名称');

        $this->addOption('type', 't', InputOption::VALUE_REQUIRED, '创建视图文件类型，例如lists，edit', '');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $controller_name = strtolower(strtolower($this->input->getArgument('controller')));
        $view_name = $this->input->getArgument('view_name');
        //执行数据库部署
        try {
            if (!is_dir("app/Application/{$module_name}/View/")) {
                mkdir("app/Application/{$module_name}/View/", 0700, true);
            }
            if (!is_dir("app/Application/{$module_name}/View/{$controller_name}")) {
                mkdir("app/Application/{$module_name}/View/{$controller_name}", 0700, true);
            }
            $view_dir = "app/Application/{$module_name}/View/{$controller_name}/" . strtolower($view_name) . '.html';
            if (file_exists($view_dir)) {
                $this->output->error("View file exist");

                return;
            }
            $type = $this->input->getOption('type');
            if (!$type) {
                $this->output->error('请输入 -t 参数，需要什么类型的视图文件');

                return;
            }
            $view_stub_path = BASE_PATH . "/app/Application/Demo/View/demo/{$type}.html";
            if (!file_exists($view_stub_path)) {
                $this->output->error("文件 {$view_stub_path} 不存在");

                return;
            }
            $view_stub = file_get_contents($view_stub_path);
            file_put_contents($view_dir, $view_stub);
            $this->output->success('create ' . $view_dir . ' success ');
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
