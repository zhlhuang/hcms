<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateQueueListTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('queue_list', function (Blueprint $table) {
            $table->bigIncrements('queue_id');
            $table->string('class_name', 256)
                ->default('')
                ->nullable(false)
                ->comment('执行job类名');
            $table->string('method', 256)
                ->default('')
                ->nullable(false)
                ->comment('执行job方法名称');
            $table->string('params', 2048)
                ->default('{}')
                ->nullable(false)
                ->comment('方法参数');
            $table->string('params_md5', 128)
                ->default('')
                ->nullable(false)
                ->comment('方法参数md5标示');
            $table->tinyInteger('status')
                ->default(0)
                ->nullable(false)
                ->comment('执行状态 0 未执行、1已执行、2执行失败');
            $table->string('error_msg', 1024)
                ->default('')
                ->nullable(false)
                ->comment('执行失败信息');
            $table->string('error_data', 2048)
                ->default('')
                ->nullable(false)
                ->comment('执行失败信息详细信息');
            $table->integer('process_time')
                ->default(0)
                ->nullable(true)
                ->comment('执行时间');
            $table->integer('process_count')
                ->default(1)
                ->nullable(false)
                ->comment('执行次数');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue_list');
    }
}
