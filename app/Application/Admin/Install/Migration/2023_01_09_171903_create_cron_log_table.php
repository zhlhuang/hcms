<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateCronLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cron_log', function (Blueprint $table) {
            $table->bigIncrements('log_id');
            $table->string('task_class', 512)
                ->default('')
                ->nullable(false)
                ->comment('执行任务的task类');
            $table->string('cron_name', 128)
                ->default('')
                ->nullable(false)
                ->comment('执行任务的名称');
            $table->string('cron_rule', 128)
                ->default('')
                ->nullable(false)
                ->comment('执行任务的规则');
            $table->string('cron_memo', 512)
                ->default('')
                ->nullable(false)
                ->comment('执行任务的说明');
            $table->tinyInteger('result')
                ->default(0)
                ->nullable(false)
                ->comment('执行结果，1为正常，0抛出异常');
            $table->string('result_msg', 512)
                ->default('')
                ->nullable(false)
                ->comment('直接结果，正常默认为ok,异常则为异常信息');
            $table->integer('execute_time')
                ->default(0)
                ->nullable(false)
                ->comment('执行时间');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cron_log');
    }
}
