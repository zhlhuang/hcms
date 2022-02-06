<?php
/**
 * Created by PhpStorm.
 * User: zhlhuang
 * Date: 2022/2/5
 * Time: 17:14.
 */

/**
 * @param $uri
 * @param $params
 * @return string
 */
function url($uri, $params = null): string
{
    $url = "/" . $uri;
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

    return $url;
}