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

class CreateDeptTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dept', function (Blueprint $table) {
            
            $table->comment('部门信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'parent_id', ['unsigned' => true, 'comment' => '父ID']);
            $table->addColumn('string', 'level', ['length' => 500, 'comment' => '组级集合']);
            $table->addColumn('string', 'name', ['length' => 30, 'comment' => '部门名称']);
            $table->addColumn('string', 'leader', ['length' => 20, 'comment' => '负责人'])->nullable();
            $table->addColumn('string', 'phone', ['length' => 11, 'comment' => '联系电话'])->nullable();
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dept');
    }
}
