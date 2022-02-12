<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUploadFileGroupTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_file_group', function (Blueprint $table) {
            $table->bigIncrements('group_id');
            $table->string('group_name', 128)
                ->nullable(false)
                ->default('')
                ->comment('分组名称');
            $table->string('file_type', 32)
                ->nullable(false)
                ->default('image')
                ->comment('文件类型，image图片、video视频、doc文档等');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_file_group');
    }
}
