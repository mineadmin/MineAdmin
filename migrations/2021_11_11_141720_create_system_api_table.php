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

class CreateSystemApiTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_api', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('接口表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'group_id', ['unsigned' => true, 'comment' => '接口组ID']);
            $table->addColumn('string', 'name', ['length' => 32, 'comment' => '接口名称']);
            $table->addColumn('string', 'access_name', ['length' => 64, 'comment' => '接口访问名称']);
            $table->addColumn('string', 'class_name', ['length' => 128, 'comment' => '类命名空间']);
            $table->addColumn('string', 'method_name', ['length' => 128, 'comment' => '方法名']);
            $table->addColumn('smallInteger', 'auth_mode', ['default' => 1, 'comment' => '认证模式 (1简易 2复杂)']);
            $table->addColumn('char', 'request_mode', ['length' => 1, 'default' => 'A', 'comment' => '请求模式 (A 所有 P POST G GET)']);
            $table->addColumn('text', 'description', ['comment' => '接口说明介绍'])->nullable();
            $table->addColumn('text', 'response', ['comment' => '返回内容示例'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('group_id');
            $table->index('access_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_api');
    }
}
