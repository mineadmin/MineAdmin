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

class CreateOperLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('oper_log', function (Blueprint $table) {
            
            $table->comment('操作日志表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->addColumn('string', 'method', ['length' => 20, 'comment' => '请求方式']);
            $table->addColumn('string', 'router', ['length' => 500, 'comment' => '请求路由']);
            $table->addColumn('string', 'service_name', ['length' => 30, 'comment' => '业务名称']);
            $table->addColumn('ipAddress', 'ip', ['comment' => '请求IP地址'])->nullable();
            $table->addColumn('string', 'ip_location', ['length' => 255, 'comment' => 'IP所属地'])->nullable();
            $table->addColumn('text', 'request_data', ['comment' => '请求数据'])->nullable();
            $table->addColumn('string', 'response_code', ['length' => 5, 'comment' => '响应状态码'])->nullable();
            $table->addColumn('text', 'response_data', ['comment' => '响应数据'])->nullable();
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->index('username');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('oper_log');
    }
}
