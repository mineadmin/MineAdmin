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
        Schema::create('user_operation_log', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->comment('操作日志表');
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->addColumn('string', 'method', ['length' => 20, 'comment' => '请求方式']);
            $table->addColumn('string', 'router', ['length' => 500, 'comment' => '请求路由']);
            $table->addColumn('string', 'service_name', ['length' => 30, 'comment' => '业务名称']);
            $table->addColumn('ipAddress', 'ip', ['comment' => '请求IP地址'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_operation_log');
    }
};
