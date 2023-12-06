<?php

declare(strict_types=1);

namespace App\Command;

use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use function Hyperf\Config\config;
/**
 * 创建模块
 */
#[Command]
class HcmsCreate extends HyperfCommand
{
    public function __construct()
    {
        parent::__construct('Hcms:create');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms create module');
        //安装指令
        $this->addArgument('module', InputArgument::REQUIRED, '创建模块名称');
        $this->addOption('migration', 'm', InputOption::VALUE_OPTIONAL, '创建迁移表名称', '');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $module_dir = "app/Application/{$module_name}/";
        try {
            //如果模块不存在，执行创建模块操作
            if (!is_dir($module_dir)) {
                $this->output->info("create module {$module_name} ...");
                //创建模块的Controller目录
                $this->call("gen:controller", [
                    "name" => "{$module_name}Controller",
                    '-N' => "App\Application\\{$module_name}\\Controller"
                ]);
                //创建模块的Migration目录
                $install_dir = $module_dir . '/Install/';

                if (mkdir($install_dir . '/Migration', 0700, true)) {
                    $setting_stub = file_get_contents(__DIR__ . '/stub/setting.stub');
                    $access_stub = file_get_contents(__DIR__ . '/stub/access.stub');
                    $config_stub = file_get_contents(__DIR__ . '/stub/config.stub');
                    file_put_contents($install_dir . 'access.php', $access_stub);
                    file_put_contents($install_dir . 'setting.php', $setting_stub);
                    file_put_contents($install_dir . 'config.php', $config_stub);
                }
            }

            $migration = $this->input->getOption('migration');
            if ($migration !== '') {
                //创建迁移表
                $migration_dir = $module_dir . '/Install/Migration/';
                $this->call('gen:migration', [
                    '--create' => $migration,
                    '--path' => $migration_dir,
                    'name' => "create_{$migration}_table"
                ]);
            }
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }
}
