<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/5
 * Time: 17:14.
 */

declare(strict_types=1);

use Hyperf\Context\Context;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @param      $uri
 * @param      $params
 * @param bool $with_domain
 * @return string
 */
function url($uri, $params = null, bool $with_domain = false): string
{
    if (str_starts_with($uri, "/")) {
        $url = $uri;
    } else {
        $url = "/" . $uri;
    }
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
        $scheme = getScheme();
        $host = $request->getUri()
            ->getHost();
        $port = $request->getUri()
            ->getPort();
        $domain = $scheme . '://' . $host . ($port ? (":" . $port) : '');
        $url = $domain . $url;
    }

    return $url;
}


/**
 * 获取IP地址
 *
 * @return string
 */
function getIp(): string
{
    try {
        $request = Context::get(ServerRequestInterface::class);
        $headers = $request->getHeaders();
        if (isset($headers['x-forwarded-for'][0])) {
            return $headers['x-forwarded-for'][0];
        } elseif (isset($headers['x-real-ip'][0])) {
            return $headers['x-real-ip'][0];
        } else {
            $server_params = $request->getServerParams();

            return $server_params['remote_addr'] ?? '';
        }
    } catch (\Throwable $exception) {
        return "";
    }
}


/**
 * nginx 代理的时候 getUri()->getScheme() 获取有误
 *
 * @return mixed|string
 */
function getScheme(): mixed
{
    try {
        $request = Context::get(ServerRequestInterface::class);
        $headers = $request->getHeaders();
        if (isset($headers['scheme'][0])) {
            return $headers['scheme'][0];
        } else {
            $server_params = $request->getServerParams();

            return !empty($server_params['https']) && $server_params['https'] !== 'off' ? 'https' : 'http';
        }
    } catch (\Throwable $exception) {
        return "http";
    }
}

