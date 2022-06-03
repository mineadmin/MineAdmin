<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

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
            $table->addColumn('char', 'is_hidden', ['length' => 1,'default' => '1', 'comment' => '是否隐藏 (0是 1否)']);
            $table->addColumn('char', 'type', ['length' => 1, 'default' => '', 'comment' => '菜单类型, (M菜单 B按钮 L链接 I iframe)']);
            $table->addColumn('char', 'status', ['length' => 1, 'default' => '0', 'comment' => '状态 (0正常 1停用)'])->nullable();
            $table->addColumn('tinyInteger', 'sort', ['unsigned' => true, 'default' => 0, 'comment' => '排序'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
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
