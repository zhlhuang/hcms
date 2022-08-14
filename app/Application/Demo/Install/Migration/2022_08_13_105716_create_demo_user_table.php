<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateDemoUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('demo_user', function (Blueprint $table) {
            $table->bigIncrements('demo_user_id');
            $table->string('username', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录用户名');
            $table->string('password', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录密码');
            $table->string('login_ip', 32)
                ->default('')
                ->nullable(false)
                ->comment('登录IP地址');
            $table->timestamp('login_time')
                ->nullable(false)
                ->comment('登录时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demo_user');
    }
}
