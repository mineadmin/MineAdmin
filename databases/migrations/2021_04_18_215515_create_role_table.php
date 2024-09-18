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

class CreateRoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('role', static function (Blueprint $table) {
            $table->comment('角色信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'name', ['length' => 30, 'comment' => '角色名称']);
            $table->addColumn('string', 'code', ['length' => 100, 'comment' => '角色代码']);
            $table->addColumn(
                'smallInteger',
                'data_scope',
                [
                    'length' => 1,
                    'default' => 1,
                    'comment' => '数据范围（1：全部数据权限 2：自定义数据权限 3：本部门数据权限 4：本部门及以下数据权限 5：本人数据权限）',
                ]
            )->nullable();
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->authorBy();
            $table->datetimes();

            $table->string('remark')->comment('备注')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role');
    }
}
