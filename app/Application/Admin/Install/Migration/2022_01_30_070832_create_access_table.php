<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAccessTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('access', function (Blueprint $table) {
            $table->bigIncrements('access_id');
            $table->integer('parent_access_id')
                ->default(0)
                ->comment('父级权限id');
            $table->string('access_name', 128)
                ->default('')
                ->comment('权限名称');
            $table->string('uri', 256)
                ->default('')
                ->comment('访问Uri');
            $table->string('params', 256)
                ->default('')
                ->comment('参数');
            $table->integer('sort')
                ->default(100)
                ->comment('排序');
            $table->tinyInteger('is_menu')
                ->default(1)
                ->comment('是否菜单 0否（只作为权限校验使用），1是');
            $table->string('menu_icon')
                ->default('')
                ->comment('菜单图片，自带elementUI icon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('access');
    }
}
