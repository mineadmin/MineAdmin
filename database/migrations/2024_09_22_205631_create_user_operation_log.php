<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_operation_log', function (Blueprint $table): void {
            $table->comment('操作日志表');
            $table->id()->comment('主键');
            $table->string('username', 20)->index()->comment('用户名');
            $table->string('method', 20)->comment('请求方式');
            $table->string('router', 500)->comment('请求路由');
            $table->string('service_name', 30)->comment('业务名称');
            $table->ipAddress('ip')->nullable()->comment('请求IP地址');
            $table->string('ip_location')->nullable()->comment('IP归属地');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark')->nullable()->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_operation_log');
    }
};
