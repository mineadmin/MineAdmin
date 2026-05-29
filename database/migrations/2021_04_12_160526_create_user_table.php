<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table): void {
            $table->comment('用户信息表');
            $table->id()->comment('用户ID,主键');
            $table->string('username', 20)->unique()->comment('用户名');
            $table->string('password', 100)->comment('密码');
            $table->string('user_type', 3)->default('100')->comment('用户类型:100=系统用户');
            $table->string('nickname', 30)->default('')->comment('用户昵称');
            $table->string('phone', 11)->default('')->comment('手机');
            $table->string('email', 50)->default('')->comment('用户邮箱');
            $table->string('avatar')->default('')->comment('用户头像');
            $table->string('signed')->default('')->comment('个人签名');
            $table->tinyInteger('status')->default(1)->comment('状态:1=正常,2=停用');
            $table->ipAddress('login_ip')->default('127.0.0.1')->comment('最后登陆IP');
            $table->timestamp('login_time')->useCurrent()->comment('最后登陆时间');
            $table->json('backend_setting')->nullable()->comment('后台设置数据');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建者');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark')->default('')->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
