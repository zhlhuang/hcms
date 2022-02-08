### 创建数据库
`php bin/hyperf.php  gen:migration --create admin_user --path="app/Application/Admin/Install/Migration" create_admin_user_table`

### 创建模型
`php bin/hyperf.php gen:model admin_role --path="app/Application/Admin/Model"`