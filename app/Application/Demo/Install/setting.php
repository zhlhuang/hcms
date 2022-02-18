<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/17 13:45
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

return [
    'demo' => [
        [
            'setting_key' => 'demo_string',
            'setting_description' => '字符串类型配置示例',
            'setting_value' => '示例字符串',
            'type' => 'string',
        ],
        [
            'setting_key' => 'demo_number',
            'setting_description' => '数字类型配置示例',
            'setting_value' => 12,
            'type' => 'number',
        ],
        [
            'setting_key' => 'demo_json',
            'setting_description' => 'Json类型配置示例',
            'setting_value' => '{"msg":"ok"}',
            'type' => 'json',
        ]
    ]
];