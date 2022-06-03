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

class CreateSystemQueueMessageReceiveTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_queue_message_receive', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('队列消息接收人表');
            $table->addColumn('bigInteger', 'message_id', ['unsigned' => true, 'comment' => '队列消息主键']);
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '接收用户主键']);
            $table->addColumn('char', 'read_status', ['length' => 1, 'default' => '0', 'comment' => '已读状态 (0未读 1已读)'])->nullable();
            $table->primary(['message_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_queue_message_receive');
    }
}
