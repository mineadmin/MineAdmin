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

class CreateSystemUserTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_user', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('用户信息表');
            $table->bigIncrements('id')->comment('用户ID，主键');
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->addColumn('string', 'password', ['length' => 100, 'comment' => '密码']);
            $table->addColumn('string', 'user_type', ['length' => 3, 'comment' => '用户类型：(100系统用户)', 'default' => '100'])->nullable();
            $table->addColumn('string', 'nickname', ['length' => 30, 'comment' => '用户昵称'])->nullable();
            $table->addColumn('string', 'phone', ['length' => 11, 'comment' => '手机'])->nullable();
            $table->addColumn('string', 'email', ['length' => 50, 'comment' => '用户邮箱'])->nullable();
            $table->addColumn('string', 'avatar', ['length' => 255, 'comment' => '用户头像'])->nullable();
            $table->addColumn('string', 'signed', ['length' => 255, 'comment' => '个人签名'])->nullable();
            $table->addColumn('string', 'dashboard', ['length' => 100, 'comment' => '后台首页类型'])->nullable();
            $table->addColumn('bigInteger', 'dept_id', ['unsigned' => true, 'comment' => '部门ID'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->nullable();
            $table->addColumn('ipAddress', 'login_ip', ['comment' => '最后登陆IP'])->nullable();
            $table->addColumn('timestamp', 'login_time', ['comment' => '最后登陆时间'])->nullable();
            $table->addColumn('string', 'backend_setting', ['length' => 500, 'comment' => '后台设置数据'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->unique('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_user');
    }
}
