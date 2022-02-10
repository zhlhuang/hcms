<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->bigIncrements('setting_id');
            $table->string('setting_key', 32)
                ->nullable(false)
                ->default('')
                ->comment('设置关键字，使用 {group}_{name} 格式');
            $table->string('setting_description', 128)
                ->nullable(false)
                ->default('')
                ->comment('配置描述');
            $table->text('setting_value')
                ->comment('配置项值');
            $table->string('setting_group', 32)
                ->nullable(false)
                ->default('')
                ->comment('配置分组，一般是用模块名作为分组');
            $table->string('type', 16)
                ->default('string')
                ->comment('配置类型，有string、number、json');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting');
    }
}
