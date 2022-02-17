<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/17 13:45
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

return [
    [
        'parent_access_id' => 0,
        'access_name' => '示例',
        'uri' => 'demo/demo/none',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'el-icon-data-analysis',
        'children' => [
            [
                'access_name' => '列表页面',
                'uri' => 'demo/demo/lists',
                'params' => '',
                'sort' => 100,
                'is_menu' => 1,
                'menu_icon' => '',
            ],
            [
                'access_name' => '编辑页面',
                'uri' => 'demo/demo/edit',
                'params' => '',
                'sort' => 100,
                'is_menu' => 1,
                'menu_icon' => '',
            ],
            [
                'access_name' => '队列示例',
                'uri' => 'demo/demo/queue',
                'params' => '',
                'sort' => 100,
                'is_menu' => 1,
                'menu_icon' => '',
            ]
        ]
    ]
];