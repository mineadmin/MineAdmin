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

class CreateSystemQueueMessageTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_queue_message', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('队列消息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'content_id', ['unsigned' => true, 'comment' => '内容ID'])->nullable();
            $table->addColumn('string', 'content_type', ['length' => 64, 'comment' => '内容类型'])->nullable();
            $table->addColumn('string', 'title', ['length' => 255, 'comment' => '消息标题'])->nullable();
            $table->addColumn('bigInteger', 'send_by', ['unsigned' => true, 'comment' => '发送人'])->nullable();
            $table->addColumn('longtext', 'content', ['comment' => '消息内容'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index(['content_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_queue_message');
    }
}
