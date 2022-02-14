<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateUploadFileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('upload_file', function (Blueprint $table) {
            $table->bigIncrements('file_id');
            //上传驱动
            $table->string('file_drive', 32)
                ->default('local')
                ->nullable(false)
                ->comment('上传驱动、local本地、aliyun、qq腾讯云');
            $table->integer('group_id')
                ->nullable(false)
                ->default(0)
                ->comment('文件分组');
            $table->string('file_url', 1024)
                ->nullable(false)
                ->default('')
                ->comment('文件访问的Url');
            $table->string('file_path', 1024)
                ->nullable(false)
                ->default('')
                ->comment('文件存储路径');
            $table->string('file_thumb', 1024)
                ->nullable(false)
                ->default('')
                ->comment('文件缩略图访问路径');
            $table->string('file_name', 128)
                ->nullable(false)
                ->default('')
                ->comment('文件名称');
            $table->string('file_type', 32)
                ->nullable(false)
                ->default('image')
                ->comment('文件类型，image图片、video视频、doc文档等');
            $table->string('file_ext', 16)
                ->nullable(false)
                ->default('')
                ->comment('文件扩展');
            $table->integer('file_size')
                ->nullable(false)
                ->default(0)
                ->comment('文件大小（单位：字节）');
            $table->integer('upload_user_id',)
                ->nullable(false)
                ->default(0)
                ->comment('上传用户id');
            $table->string('upload_user_type', 16)
                ->nullable(false)
                ->default('')
                ->comment('上传用户类型、admin管理员、user用户');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('upload_file');
    }
}
