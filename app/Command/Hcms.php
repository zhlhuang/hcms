<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * @Command
 */
#[Command]
class Hcms extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('Hcms');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms');
        //安装指令
        $this->addOption('install', 'i', InputOption::VALUE_OPTIONAL, 'install', '');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $install_name = $this->input->getOption('install');
        if ($install_name !== '') {
            $module_name = ucfirst($install_name);
            $module_dir = "app/Application/{$module_name}/";
            //不存在该模块
            if (!is_dir($module_dir)) {
                $this->output->error("module {$module_name} is not exist");

                return;
            }
            //执行数据库部署
            try {
                $path = $module_dir . "Install/Migration";
                $this->output->info("migrate " . $path);
                $this->call('migrate', [
                    '--path' => $path
                ]);
            } catch (\Exception $exception) {
                $this->output->error($exception->getMessage());
            }
        }
    }
}
