<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role_belongs_menu', function (Blueprint $table): void {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('role_id')->comment('角色id');
            $table->unsignedBigInteger('menu_id')->comment('菜单id');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role_belongs_menu');
    }
};
