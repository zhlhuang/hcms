<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class AddIsProtectToUploadFileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('upload_file', function (Blueprint $table) {
            $table->tinyInteger('is_protect')
                ->default(0)
                ->nullable(false)
                ->comment('是否受保护：1存storage禁止外部访问，0普通文件')
                ->after('acl');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('upload_file', function (Blueprint $table) {
            $table->dropColumn(['is_protect']);
        });
    }
}
