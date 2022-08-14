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

class CreateSystemQueueLogTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_queue_log', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('队列日志表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'exchange_name', ['length' => 32, 'comment' => '交换机名称']);
            $table->addColumn('string', 'routing_key_name', ['length' => 32, 'comment' => '路由名称']);
            $table->addColumn('string', 'queue_name', ['length' => 64, 'comment' => '队列名称']);
            $table->addColumn('longtext', 'queue_content', ['comment' => '队列数据'])->nullable();
            $table->addColumn('text', 'log_content', ['comment' => '队列日志'])->nullable();
            $table->addColumn('smallInteger', 'produce_status', ['default' => 1, 'comment' => '生产状态 1:未生产 2:生产中 3:生产成功 4:生产失败 5:生产重复'])->nullable();
            $table->addColumn('smallInteger', 'consume_status', ['default' => 1, 'comment' => '消费状态 1:未消费 2:消费中 3:消费成功 4:消费失败 5:消费重复'])->nullable();
            $table->addColumn('integer', 'delay_time', ['unsigned' => true, 'comment' => '延迟时间（秒）']);
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_queue_log');
    }
}
