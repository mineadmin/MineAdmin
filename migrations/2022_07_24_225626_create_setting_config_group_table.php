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

class CreateSettingConfigGroupTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_config_group', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('参数配置分组表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'name', ['length' => 32, 'comment' => '配置组名称']);
            $table->addColumn('string', 'code', ['length' => 64, 'comment' => '配置组标识']);
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->timestamps();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_config_group');
    }
}
