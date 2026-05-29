<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table): void {
            $table->comment('菜单信息表');
            $table->id()->comment('主键');
            $table->unsignedBigInteger('parent_id')->comment('父ID');
            $table->string('name', 50)->default('')->unique()->comment('菜单名称');
            $table->json('meta')->nullable()->comment('附加属性');
            $table->string('path', 60)->default('')->comment('路径');
            $table->string('component', 150)->default('')->comment('组件路径');
            $table->string('redirect', 100)->default('')->comment('重定向地址');
            $table->tinyInteger('status')->default(1)->comment('状态:1=正常,2=停用');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建者');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark', 60)->default('')->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
};
