<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSettingCrontabTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_crontab', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('定时任务信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'name', ['length' => 100, 'comment' => '任务名称'])->nullable();
            $table->addColumn(
                'smallInteger', 'type',
                ['default' => 4, 'comment' => '任务类型 (1 command, 2 class, 3 url, 4 eval)']
            )->nullable();
            $table->addColumn('string', 'target', ['length' => 500, 'comment' => '调用任务字符串'])->nullable();
            $table->addColumn('string', 'parameter', ['length' => 1000, 'comment' => '调用任务参数'])->nullable();
            $table->addColumn('string', 'rule', ['length' => 32, 'comment' => '任务执行表达式'])->nullable();
            $table->addColumn(
                'smallInteger', 'singleton',
                ['default' => 1, 'comment' => '是否单次执行 (1 是 2 不是)']
            )->nullable();
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_task');
    }
}
