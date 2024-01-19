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

class CreateSystemApiColumnTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_api_column', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('接口字段表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'api_id', ['unsigned' => true, 'comment' => '接口主键']);
            $table->addColumn('string', 'name', ['length' => 64, 'comment' => '字段名称']);
            $table->addColumn('smallInteger', 'type', ['default' => 1, 'comment' => '字段类型 1 请求 2 返回']);
            $table->addColumn('string', 'data_type', ['length' => 16, 'comment' => '数据类型']);
            $table->addColumn('smallInteger', 'is_required', ['default' => 1, 'comment' => '是否必填 1 非必填 2 必填']);
            $table->addColumn('string', 'default_value', ['length' => 500, 'comment' => '默认值'])->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->nullable();
            $table->addColumn('string', 'description', ['length' => 500, 'comment' => '字段说明'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index(['api_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_api_column');
    }
}
