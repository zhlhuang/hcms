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
        'access_name' => '概览',
        'uri' => 'admin/main/index',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'el-icon-data-analysis',
    ],
    [
        'parent_access_id' => 0,
        'access_name' => '设置',
        'uri' => 'admin/setting/none',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'el-icon-setting',
        'children' => [
            [
                'access_name' => '菜单与权限',
                'uri' => 'admin/access/index',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '新增/编辑菜单/权限',
                        'uri' => 'admin/access/edit',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                    [
                        'access_name' => '删除菜单/权限',
                        'uri' => 'admin/access/delete',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                ]
            ],
            [
                'access_name' => '角色管理',
                'uri' => 'admin/role/index',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '新增/编辑角色',
                        'uri' => 'admin/role/edit',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                    [
                        'access_name' => '删除角色',
                        'uri' => 'admin/role/delete',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                ]
            ],
            [
                'access_name' => '管理员',
                'uri' => 'admin/user/index',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '新增/编辑管理员',
                        'uri' => 'admin/user/edit',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                    [
                        'access_name' => '删除管理员',
                        'uri' => 'admin/user/delete',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                ]
            ],
            [
                'access_name' => '配置管理',
                'uri' => 'admin/setting/none',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '站点管理',
                        'uri' => 'admin/setting/site',
                        'sort' => 100,
                        'is_menu' => 1,
                    ],
                    [
                        'access_name' => '配置列表',
                        'uri' => 'admin/setting/index',
                        'sort' => 100,
                        'is_menu' => 1,
                    ],
                    [
                        'access_name' => '新增/编辑配置',
                        'uri' => 'admin/setting/edit',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                    [
                        'access_name' => '删除配置',
                        'uri' => 'admin/setting/delete',
                        'sort' => 100,
                        'is_menu' => 0,
                    ],
                ]
            ],
            [
                'access_name' => '上传管理',
                'uri' => 'admin/upload/none',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '上传设置',
                        'uri' => 'admin/upload/setting',
                        'sort' => 100,
                        'is_menu' => 1,
                    ],
                    [
                        'access_name' => '文件列表',
                        'uri' => 'admin/upload/index',
                        'sort' => 100,
                        'is_menu' => 1,
                    ],
                ]
            ],
        ]
    ],
    [
        'parent_access_id' => 0,
        'access_name' => '系统',
        'uri' => 'admin/system/none',
        'params' => '',
        'sort' => 100,
        'is_menu' => 1,
        'menu_icon' => 'el-icon-monitor',
        'children' => [
            [
                'access_name' => '队列',
                'uri' => 'admin/queue/none',
                'sort' => 100,
                'is_menu' => 1,
                'children' => [
                    [
                        'access_name' => '执行记录',
                        'uri' => 'admin/queue/index',
                        'sort' => 100,
                        'is_menu' => 1,
                    ],
                    [
                        'access_name' => '队列状态',
                        'uri' => 'admin/queue/status',
                        'sort' => 100,
                        'is_menu' => 1,
                    ]
                ],
            ],
            [
                'access_name' => '日志',
                'uri' => 'admin/log/index',
                'sort' => 100,
                'is_menu' => 1,
            ],
            [
                'access_name' => '缓存',
                'uri' => 'admin/cache/index',
                'sort' => 100,
                'is_menu' => 1,
            ]
        ],
    ],
];