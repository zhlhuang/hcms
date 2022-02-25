<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @Command
 */
#[Command]
class HcmsModel extends HyperfCommand
{

    public function __construct()
    {
        parent::__construct('Hcms:model');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module model file');
        //安装指令
        $this->addArgument('module', InputArgument::REQUIRED, '执行的模块');
        $this->addArgument('table_name', InputArgument::REQUIRED, '创建model的表名');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $table_name = $this->input->getArgument('table_name');
        //执行数据库部署
        try {
            $path = "app/Application/{$module_name}/Model";
            $this->call('gen:model', [
                'table' => $table_name,
                '--path' => $path
            ]);
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
