<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Admin\Model\Access;
use App\Application\Admin\Model\Setting;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @Command
 */
#[Command]
class HcmsInstall extends HyperfCommand
{
    public function __construct()
    {
        parent::__construct('Hcms:install');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('This is Hcms install module');
        //安装指令

        $this->addArgument('module', InputArgument::REQUIRED, '安装模块');
    }

    public function handle()
    {
        if (config('app_env', 'dev') !== 'dev') {
            $this->output->error('命令行执行必须在开发环境，当前环境是：' . config('app_env', 'dev'));

            return;
        }
        $module_name = ucfirst(strtolower($this->input->getArgument('module')));
        $module_dir = "app/Application/{$module_name}/";
        //通过远程下载模块
        $this->downloadFromRemote($module_dir, $module_name);
        //不存在该模块
        if (!is_dir($module_dir)) {
            $this->output->writeln("<error>模块 {$module_name} 不存在<error>");

            return;
        }
        try {
            $module_install_dir = $module_dir . "Install/";
            $this->output->writeln('安装检查...');

            if (!$this->check($module_install_dir)) {
                return;
            }

            $this->output->writeln('正在安装模块...');
            sleep(2);
            $this->migrate($module_install_dir);
            //执行权限菜单接口
            $access_file = $module_install_dir . '/access.php';
            if (file_exists($access_file)) {
                $access_list = include_once $access_file;
                if (!is_array($access_list)) {
                    throw new \Exception('权限文件access.php 格式错误');
                }
                //创建权限
                $this->createAccess($access_list);
                $this->output->writeln('<info>创建菜单/权限完成<info>');
            }

            $setting_file = $module_install_dir . '/setting.php';
            if (file_exists($setting_file)) {
                $setting_list = include_once $setting_file;
                if (!is_array($setting_list)) {
                    throw new \Exception('配置文件setting.php 格式错误');
                }
                //创建权限
                $this->createSetting($setting_list);
                $this->output->writeln('<info>创建配置完成<info>');
            }
        } catch (\Exception $exception) {
            $this->output->error($exception->getMessage());
        }
    }

    private function migrate($module_install_dir)
    {
        //执行数据库部署
        $migrate_path = $module_install_dir . "Migration";
        $this->output->info("migrate " . $migrate_path);
        $this->call('migrate', [
            '--path' => $migrate_path
        ]);
    }

    /**
     * 安装检查
     *
     * @param $module_install_dir
     * @return bool
     */
    private function check($module_install_dir): bool
    {
        $config_file = $module_install_dir . '/config.php';
        if (!file_exists($module_install_dir . '/config.php')) {
            return true;
        }
        $config_list = include_once $config_file;
        $require = $config_list['require'] ?? [];
        //依赖检测
        if (!empty($require['module'])) {
            $app_path = BASE_PATH . '/app/Application/';
            $install_module_list = scandir($app_path);
            foreach ($require['module'] as $value) {
                $module_name = ucfirst($value);
                if (!in_array($module_name, $install_module_list)) {
                    $this->output->writeln("<error>所需模块{$module_name}未安装 ×</error>");

                    return false;
                }
            }
        }

        $this->output->writeln('<info>依赖模块 √<info>');
        //版本检测
        if (!empty($require['hcms_version'])) {
            $need_version = $require['hcms_version'];
            //检测hcms版本
            $hcms_version = config('version.hcms_version', '1.0.0');
            $need_version_array = explode('.', $need_version);
            $hcms_version_array = explode('.', $hcms_version);
            $result = false;
            foreach ($hcms_version_array as $key => $value) {
                if (intval($value) > intval($need_version_array[$key])) {
                    $result = true;
                    break;
                } elseif ($value == $need_version_array[$key]) {
                    $result = true;
                } else {
                    $result = false;
                    break;
                }
            }
            if ($result) {
                $this->output->writeln("需要版本{$need_version}现在版本{$hcms_version} √");
            } else {
                $this->output->writeln("<error>需要版本{$need_version}现在版本{$hcms_version} ×<error>");

                return false;
            }
        }
        //检查composer 依赖安装
        $composer = json_decode(file_get_contents(BASE_PATH . '/composer.json'), true);
        if (!empty($require['composer'])) {
            $composer_require = $composer['require'] ?? [];
            foreach ($require['composer'] as $name => $version) {
                if (empty($composer_require[$name])) {
                    $this->output->writeln("<error>需要执行 \" composer require  {$name} {$version} \" 安装</error>");

                    return false;
                }
            }
        }
        $this->output->writeln('<info>composer依赖 √<info>');

        return true;
    }

    /**
     * 执行远程获取模块
     *
     * @param $module_dir
     * @param $module_name
     */
    private function downloadFromRemote($module_dir, $module_name)
    {
        if (is_dir($module_dir)) {
            $is_remote = 0;
        } else {
            $is_remote = (int)$this->output->ask('是否请求远程获取模块？[是:1,否:0] ?', '0');
        }
        if ($is_remote === 1) {
            //github下载地址
            $download_url = "https://github.com/hcms-module/" . (strtolower($module_name)) . "/archive/master.zip";
            $this->output->writeln("<info>正在下载 {$download_url} ...<info>");
            try {
                file_put_contents(__DIR__ . "/{$module_name}.zip", file_get_contents($download_url));
            } catch (\Throwable $exception) {
                $this->output->writeln("<error>下载失败，有可能是网络原因，可以稍后再试！</error>");

                return;
            }
            $zip_file = __DIR__ . "/{$module_name}.zip";
            if (file_exists($zip_file)) {
                $zip = new \ZipArchive();
                $zip->open($zip_file);
                $zip->extractTo(__DIR__ . '/../Application');
                $unzip_name = $zip->statIndex(0)['name'] ?? '';
                $zip->close();
                $unzip_dir = __DIR__ . "/../Application/" . $unzip_name;
                rename($unzip_dir, $module_dir);
                if (is_dir($module_dir)) {
                    $this->output->writeln('模块下载成功');
                } else {
                    $this->output->writeln("<error>解压模块失败，你可以自己访问 {$download_url} 下载<error>");
                }
                //模块处理成功，删除下载文件
                unlink($zip_file);
            } else {
                $this->output->writeln("<error>下载模块失败，你可以自己访问 {$download_url} 下载<error>");
            }
        }
    }

    /**
     * 创建配置
     *
     * @param array $setting_list
     */
    private function createSetting(array $setting_list = [])
    {
        foreach ($setting_list as $group => $settings) {
            foreach ($settings as $setting) {
                Setting::firstOrCreate([
                    'setting_key' => $setting['setting_key'],
                    'setting_group' => $group,
                ], [
                    'setting_description' => $setting['setting_description'] ?? '',
                    'setting_value' => $setting['setting_value'] ?? '',
                    'type' => $setting['type'] ?? '',
                ]);
            }
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
                'params' => $access['params'] ?? ''
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
