# 介绍

Hcms 是一个基于 [Hyperf](https://hyperf.io/) 框架的项目开发管理系统 , 在Hyperf超高速且灵活的特性加持下，构建一个快速开发、模块复用的项目开发管理系统。

# 依赖

- PHP >= 7.4
- Swoole PHP extension >= 4.5，and Disabled `Short Name`
- OpenSSL PHP extension
- JSON PHP extension
- PDO PHP extension
- Redis PHP extension

# 安装
- composer 创建项目
```shell
composer create-project zhlhuang/hcms
```
- 配置 .env 数据库和Redis
```
APP_NAME=hcms
APP_ENV=dev

DB_DRIVER=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=hcms
DB_USERNAME=root
DB_PASSWORD=
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
DB_PREFIX=

REDIS_HOST=localhost
REDIS_AUTH=(null)
REDIS_PORT=6379
REDIS_DB=0
```
- 执行hcms安装 Admin 模块
```shell
php bin/hyperf.php hcms:install admin
```

#启动
```shell
composer start

//开发启动
composer watch
```

#访问
访问 `http://127.0.0.1:9501/admin/index/index`

默认管理的账号是  admin、密码是 123123


# 文档
[https://www.yuque.com/huangzhenlian/hcms](https://www.yuque.com/huangzhenlian/hcms)
