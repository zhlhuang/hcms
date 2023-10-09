<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAdminLoginRecordTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_login_record', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('username', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录用户名');
            $table->tinyInteger('login_result')
                ->default(0)
                ->nullable(false)
                ->comment('登录结果，1成功、0失败');
            $table->string('result_msg', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录结果说明');
            $table->string('ip', 128)
                ->default('')
                ->nullable(false)
                ->comment('登录来源IP');
            $table->string('user_agent', 1024)
                ->default('')
                ->nullable(false)
                ->comment('用户端信息');
            $table->datetimes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_login_record');
    }
}
