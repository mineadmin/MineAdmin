<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

class CreateUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user', function (Blueprint $table) {
            $table->comment('用户信息表');
            $table->bigIncrements('id')->comment('用户ID，主键');
            $table->string('username', 20)->unique()->comment('用户名');
            $table->string('password', 100)->comment('密码');
            $table->string('user_type', 3)->default('100')->comment('用户类型：(100系统用户)');
            $table->string('nickname', 30)->default('')->comment('用户昵称');
            $table->string('phone', 11)->default('')->comment('手机');
            $table->string('email', 50)->default('')->comment('用户邮箱');
            $table->string('avatar', 255)->default('')->comment('用户头像');
            $table->string('signed', 255)->default('')->comment('个人签名');
            $table->string('dashboard', 100)->default('')->comment('后台首页类型');
            $table->tinyInteger('status')->default(1)->comment('状态 (1正常 2停用)');
            $table->ipAddress('login_ip')->default('')->comment('最后登陆IP');
            $table->timestamp('login_time')->useCurrent()->comment('最后登陆时间');
            $table->json('backend_setting')->nullable()->comment('后台设置数据');
            $table->bigInteger('created_by')->default(0)->comment('创建者');
            $table->bigInteger('updated_by')->default(0)->comment('更新者');
            $table->timestamps();
            $table->softDeletes();
            $table->string('remark', 255)->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
}
