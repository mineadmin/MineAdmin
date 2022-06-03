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

class CreateSystemApiColumnTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_api_column', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('接口字段表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'api_id', ['unsigned' => true, 'comment' => '接口主键']);
            $table->addColumn('string', 'name', ['length' => 64, 'comment' => '字段名称']);
            $table->addColumn('char', 'type', ['length' => 1, 'default' => '0', 'comment' => '字段类型 0 请求 1 返回']);
            $table->addColumn('string', 'data_type', ['length' => 16, 'comment' => '数据类型']);
            $table->addColumn('char', 'is_required', ['length' => 1, 'default' => '0', 'comment' => '是否必填 0 非必填 1 必填']);
            $table->addColumn('string', 'default_value', ['length' => 500, 'comment' => '默认值'])->nullable();
            $table->addColumn('char', 'status', ['length' => 1, 'default' => '0', 'comment' => '状态 (0正常 1停用)'])->nullable();
            $table->addColumn('string', 'description', ['length' => 500, 'comment' => '字段说明'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index(['api_id', 'type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_api_column');
    }
}
