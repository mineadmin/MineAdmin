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

class CreateSettingConfigTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_config', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('参数配置信息表');
            $table->addColumn('bigInteger', 'group_id', ['comment' => '组id']);
            $table->addColumn('string', 'key', ['length' => 32, 'comment' => '配置键名'])->primary();
            $table->addColumn('string', 'value', ['length' => 255, 'comment' => '配置值'])->nullable();
            $table->addColumn('string', 'name', ['length' => 255, 'comment' => '配置名称'])->nullable();
            $table->addColumn('string', 'input_type', ['length' => 32, 'comment' => '数据输入类型'])->nullable();
            $table->addColumn('string', 'config_select_data', ['length' => 500, 'comment' => '配置选项数据'])->nullable();
            $table->addColumn('smallInteger', 'sort', ['unsigned' => true, 'default' => 0, 'comment' => '排序']);
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_config');
    }
}
