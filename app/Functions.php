<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/5
 * Time: 17:14.
 */

declare(strict_types=1);

use App\Application\Admin\Service\AdminSettingService;

use Hyperf\Context\Context;
use Hyperf\HttpMessage\Server\Request;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @param $uri
 * @param $params
 * @return string
 */
function url($uri, $params = null, $with_domain = false): string
{


    $site_dir = (new AdminSettingService)->getSiteSetting('site_dir', '/');
    $url = $site_dir . $uri;
    if (!empty($params)) {
        //带有数组参数
        if (is_array($params)) {
            $params_items = [];
            foreach ($params as $key => $value) {
                $params_items[] = $key . "=" . $value;
            }
            $url .= '?' . implode('&', $params_items);
        } else {
            //字符串参数，直接拼接
            $url .= '?' . $params;
        }
    }
    if ($with_domain) {
        $request = Context::get(ServerRequestInterface::class);
        $domain = '';
        if ($request instanceof Request) {
            $scheme = $request->getUri()
                ->getScheme();
            $host = $request->getUri()
                ->getHost();
            $port = $request->getUri()
                ->getPort();
            $domain = $scheme . '://' . $host . ($port ? (":" . $port) : '');
        }
        $url = $domain . $url;
    }

    return $url;
}