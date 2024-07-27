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
            $table->string('name', 50)->default('')->comment('菜单名称');
            $table->string('code', 100)->default('')->comment('菜单标识代码');
            $table->string('icon', 50)->default('')->comment('菜单图标');
            $table->string('route', 200)->default('')->comment('路由地址');
            $table->string('component', 255)->default('')->comment('组件路径');
            $table->char('redirect')->comment('重定向地址')->default('');
            $table->tinyInteger('is_hidden')->comment('是否隐藏 (1是 2否)')->default(1);
            $table->string('type',1)->default('')->comment('菜单类型, (M菜单 B按钮 L链接 I iframe)');
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->tinyInteger('status')->comment('状态 (1正常 2停用)')->default(1);
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->smallInteger('sort')->comment('排序')->default(0);
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->string('remark')->comment('备注')->default('');
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
