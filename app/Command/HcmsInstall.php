<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Admin\Model\Access;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
#[Command]
class HcmsInstall extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('Hcms:install');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms');
        //安装指令

        $this->addArgument('module', InputArgument::REQUIRED, '安装模块');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('This command should be executed in develop env ');

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $module_dir = "app/Application/{$module_name}/";
        //不存在该模块
        if (!is_dir($module_dir)) {
            $this->output->error("module {$module_name} is not exist");

            return;
        }
        try {
            $module_install_dir = $module_dir . "Install/";

            //执行数据库部署
            $migrate_path = $module_install_dir . "Migration";
            $this->output->info("migrate " . $migrate_path);
            $this->call('migrate', [
                '--path' => $migrate_path
            ]);

            //执行权限菜单接口
            $access_file = $module_install_dir . '/access.php';
            if (file_exists($access_file)) {
                $access_list = include_once $access_file;
                if (!is_array($access_list)) {
                    throw new \Exception('权限文件access.php 格式错误');
                }
                //创建权限
                $this->createAccess($access_list);
            }
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }

    /**
     * 创建权限和菜单
     *
     * @param array $access_list
     * @param int   $parent_access_id
     * @return void
     */
    private function createAccess(array $access_list, int $parent_access_id = 0): void
    {
        foreach ($access_list as $access) {
            $access_model = Access::firstOrCreate([
                'access_name' => $access['access_name'],
                'uri' => $access['uri'],
                'params' => $access['params']
            ], [
                'parent_access_id' => intval($access['parent_access_id'] ?? $parent_access_id),
                'sort' => $access['sort'] ?? 100,
                'is_menu' => $access['is_menu'] ?? Access::IS_MENU_YES,
                'menu_icon' => $access['menu_icon'] ?? '',
            ]);
            if (!empty($access['children'])) {
                $this->createAccess($access['children'], $access_model->access_id);
            }
        }
    }
}
