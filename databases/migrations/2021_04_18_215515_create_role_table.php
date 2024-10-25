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
            $table->string('name', 30)->comment('角色名称');
            $table->string('code', 100)->comment('角色代码')->unique();
            $table->tinyInteger('status')->comment('状态:1=正常,2=停用')->default(1);
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
