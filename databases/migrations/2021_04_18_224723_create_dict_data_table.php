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

class CreateDictDataTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dict_data', function (Blueprint $table) {
            
            $table->comment('字典数据表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'type_id', ['unsigned' => true, 'comment' => '字典类型ID']);
            $table->addColumn('string', 'label', ['length' => 50, 'comment' => '字典标签'])->nullable();
            $table->addColumn('string', 'value', ['length' => 100, 'comment' => '字典值'])->nullable();
            $table->addColumn('string', 'code', ['length' => 100, 'comment' => '字典标示'])->nullable();
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->index('type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dict_data');
    }
}
