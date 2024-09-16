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

class CreateMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->comment('菜单信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->bigInteger('parent_id')->unsigned()->comment('父ID');
            $table->char('name', 50)->default('')->comment('菜单名称');
            $table->json('meta')->comment('附加属性')->nullable();
            $table->char('path', 60)->default('')->comment('路径');
            $table->string('component', 150)->default('')->comment('组件路径');
            $table->char('redirect',100)->comment('重定向地址')->default('');
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->authorBy();
            $table->datetimes();
            $table->char('remark',60)->comment('备注')->default('');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu');
    }
}
