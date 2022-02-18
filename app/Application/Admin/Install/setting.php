<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/2/17 13:45
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

return [
    'log' => [
        [
            'setting_key' => 'log_is_open',
            'setting_description' => '操作日志记录是否开启',
            'setting_value' => 1,
            'type' => 'number',
        ]
    ],
    'upload' => [
        [
            'setting_key' => 'upload_drive',
            'setting_description' => '上传驱动',
            'setting_value' => 'local',
            'type' => 'string',
        ],
        [
            'setting_key' => 'upload_file_dir',
            'setting_description' => '文件存储路径，为了方便访问文件都是放在public目录，填写upload会存放在public/upload目录下',
            'setting_value' => 'upload',
            'type' => 'string',
        ],
        [
            'setting_key' => 'upload_allow_ext',
            'setting_description' => '允许上传的文件后缀',
            'setting_value' => 'jpg|jpeg|gif|bmp|png|doc|docx|xls|xlsx|ppt|pptx|pdf|txt|rar|zip|mp4',
            'type' => 'string',
        ],
        [
            'setting_key' => 'upload_domain',
            'setting_description' => '文件访问的域名，注意需要以 / 结尾，本地上传驱动可以使用 /，如果使用对象存储的镜像回源，直接填写对象存储的访问域名',
            'setting_value' => '/',
            'type' => 'string',
        ],
        [
            'setting_key' => 'upload_file_size',
            'setting_description' => '文件上传限制，单位KB',
            'setting_value' => 2048,
            'type' => 'number',
        ]
    ],
    'site' => [
        [
            'setting_key' => 'site_name',
            'setting_description' => '这是整个站点项目的名称',
            'setting_value' => 'Hcms',
            'type' => 'string',
        ],
        [
            'setting_key' => 'site_description',
            'setting_description' => '站点描述',
            'setting_value' => 'Hcms 是基于Hyperf 框架开发的模块化管理系统，致力于定制化项目模块化开发规范。',
            'type' => 'string',
        ],
        [
            'setting_key' => 'site_dir',
            'setting_description' => '网站根目录，一般都是/',
            'setting_value' => '/',
            'type' => 'string',
        ]
    ]
];