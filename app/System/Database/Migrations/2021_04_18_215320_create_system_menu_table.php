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

class CreateSystemMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_menu', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('菜单信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'parent_id', ['unsigned' => true, 'comment' => '父ID']);
            $table->addColumn('string', 'level', ['length' => 500, 'comment' => '组级集合']);
            $table->addColumn('string', 'name', ['length' => 50, 'comment' => '菜单名称']);
            $table->addColumn('string', 'code', ['length' => 100, 'comment' => '菜单标识代码']);
            $table->addColumn('string', 'icon', ['length' => 50, 'comment' => '菜单图标'])->nullable();
            $table->addColumn('string', 'route', ['length' => 200, 'comment' => '路由地址'])->nullable();
            $table->addColumn('string', 'component', ['length' => 255, 'comment' => '组件路径'])->nullable();
            $table->addColumn('string', 'redirect', ['length' => 255, 'comment' => '跳转地址'])->nullable();
            $table->addColumn('smallInteger', 'is_hidden', ['default' => 1, 'comment' => '是否隐藏 (1是 2否)']);
            $table->addColumn('char', 'type', ['length' => 1, 'default' => '', 'comment' => '菜单类型, (M菜单 B按钮 L链接 I iframe)']);
            $table->addColumn('smallInteger', 'status', ['default' => 1, 'comment' => '状态 (1正常 2停用)'])->nullable();
            $table->addColumn('smallInteger', 'sort', ['unsigned' => true, 'default' => 0, 'comment' => '排序'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->timestamps();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_menu');
    }
}
