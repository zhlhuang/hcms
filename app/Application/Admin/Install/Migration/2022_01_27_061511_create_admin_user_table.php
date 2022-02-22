<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAdminUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_user', function (Blueprint $table) {
            $table->bigIncrements('admin_user_id');
            //登录用户名
            $table->string('username', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录用户名');
            //登录密码
            $table->string('password', 256)
                ->nullable(false)
                ->default('')
                ->comment('密码');
            //所属角色id
            $table->integer('role_id', false)
                ->nullable(false)
                ->default(0)
                ->comment('所属角色id');
            //真实姓名
            $table->string('real_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('管理员姓名');
            $table->timestamps();
            $table->softDeletes();
        });
        \Hyperf\DbConnection\Db::table('admin_user')
            ->insert([
                'username' => 'admin',
                'password' => 'f8c7402e1ebdbc8ad37caae249710bad',
                'role_id' => 0,
                'real_name' => '系统管理员',
                'created_at' => date('Y-m-d H:i:s', time()),
                'updated_at' => date('Y-m-d H:i:s', time()),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user');
    }
}
