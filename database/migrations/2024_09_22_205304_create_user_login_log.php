<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_login_log', function (Blueprint $table): void {
            $table->comment('登录日志表');
            $table->id()->comment('主键');
            $table->string('username', 20)->index()->comment('用户名');
            $table->ipAddress('ip')->nullable()->comment('登录IP地址');
            $table->string('os')->nullable()->comment('操作系统');
            $table->string('browser')->nullable()->comment('浏览器');
            $table->smallInteger('status')->default(1)->comment('登录状态 (1成功 2失败)');
            $table->string('message', 50)->nullable()->comment('提示消息');
            $table->dateTime('login_time')->comment('登录时间');
            $table->string('remark')->nullable()->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_log');
    }
};
