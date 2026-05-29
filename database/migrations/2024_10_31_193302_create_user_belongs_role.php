<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_belongs_role', function (Blueprint $table): void {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->unsignedBigInteger('role_id')->comment('角色id');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_belongs_role');
    }
};
