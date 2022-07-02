<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/1/25
 * Time: 20:24.
 */

declare(strict_types=1);

namespace App\Application\Admin\Lib;

use App\Application\Admin\Service\AdminSettingService;
use App\Service\ApiService;
use Hyperf\Di\Annotation\Inject;

class RenderParam
{
    public string $template = '';
    public bool $layout = true;
    protected array $data = [];
    protected array $common_data = [];
    /**
     * @Inject()
     */
    protected AdminSettingService $service;

    /**
     * @Inject()
     */
    protected ApiService $api_service;


    public function __construct(array $data = [])
    {
        $this->data = $data;
        $this->common_data = [
            'site_name' => $this->service->getSiteSetting('site_name', 'Hcms'),
            'version' => config('version.version'),
            'api_encode' => $this->api_service->getEncodeData(),
            'env' => env('APP_ENV')
        ];
        if ($this->api_service->getEncodeData()) {
            $this->common_data += ['api_key' => $this->api_service->getApiKey()];
        }
    }

    public static function display(string $template = '', array $data = []): RenderParam
    {
        return (new self($data))->setTemplate($template);
    }

    /**
     * @param string $template
     * @return $this
     */
    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return array_merge($this->common_data, $this->data);
    }

    /**
     * @param array $common_data
     */
    public function setCommonData(array $common_data): void
    {
        $this->common_data = $common_data;
    }

    /**
     * @param bool $layout
     * @return $this
     */
    public function setLayout(bool $layout): self
    {
        $this->layout = $layout;

        return $this;
    }
}