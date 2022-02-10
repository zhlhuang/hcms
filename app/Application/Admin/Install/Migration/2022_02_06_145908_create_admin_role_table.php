<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateAdminRoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admin_role', function (Blueprint $table) {
            $table->bigIncrements('role_id');
            $table->string('role_name', 128)
                ->nullable(false)
                ->default('')
                ->comment('角色名称');
            $table->string('description', '512')
                ->nullable(false)
                ->default('')
                ->comment('角色描述');
            $table->integer('parent_role_id')
                ->default(0)
                ->nullable(false)
                ->comment('父角色id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_role');
    }
}
