<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAdminRoleAccessTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_role_access', function (Blueprint $table) {
            $table->bigIncrements('role_access_id');
            $table->integer('role_id')
                ->nullable(false);
            $table->integer('access_id')
                ->nullable(false);
            $table->string('access_uri', 256)
                ->default('')
                ->nullable(false)
                ->comment('菜单或权限的uri');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role_access');
    }
}
