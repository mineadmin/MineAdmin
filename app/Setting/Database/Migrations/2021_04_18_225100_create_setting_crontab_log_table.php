<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSettingCrontabLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_crontab_log', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('定时任务执行日志表');
            $table->addColumn('bigInteger', 'id', ['unsigned' => true, 'comment' => '主键']);
            $table->addColumn('bigInteger', 'crontab_id', ['unsigned' => true, 'comment' => '任务ID']);
            $table->addColumn('string', 'name', ['length'=> 255, 'comment' => '任务名称'])->nullable();
            $table->addColumn('string', 'target', ['length'=> 500, 'comment' => '任务调用目标字符串'])->nullable();
            $table->addColumn('string', 'parameter', ['length'=> 1000, 'comment' => '任务调用参数'])->nullable();
            $table->addColumn('string', 'exception_info', ['length'=> 2000, 'comment' => '异常信息'])->nullable();
            $table->addColumn('char', 'status', ['length' => 1, 'default' => '0', 'comment' => '执行状态 (0成功 1失败)'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_crontab_log');
    }
}
