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

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_login_log', static function (Blueprint $table) {
            $table->comment('登录日志表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->addColumn('ipAddress', 'ip', ['comment' => '登录IP地址'])->nullable();
            $table->addColumn('string', 'os', ['length' => 255, 'comment' => '操作系统'])->nullable();
            $table->addColumn('string', 'browser', ['length' => 255, 'comment' => '浏览器'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '登录状态 (1成功 2失败)']);
            $table->addColumn('string', 'message', ['length' => 50, 'comment' => '提示消息'])->nullable();
            $table->dateTime('login_time')->comment('登录时间');
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_login_log');
    }
};
