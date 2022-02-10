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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_user');
    }
}
