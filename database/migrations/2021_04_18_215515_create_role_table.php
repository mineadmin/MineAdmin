<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('role', function (Blueprint $table): void {
            $table->comment('角色信息表');
            $table->id()->comment('主键');
            $table->string('name', 30)->comment('角色名称');
            $table->string('code', 100)->unique()->comment('角色代码');
            $table->tinyInteger('data_scope')->default(1)->comment('数据范围');
            $table->tinyInteger('status')->default(1)->comment('状态:1=正常,2=停用');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建者');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark')->default('')->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('role');
    }
};
