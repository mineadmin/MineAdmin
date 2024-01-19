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

class CreateSystemNoticeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_notice', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('系统公告表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'message_id')->comment('消息ID');
            $table->addColumn('string', 'title', ['length' => 255, 'comment' => '标题']);
            $table->addColumn('smallInteger', 'type', ['comment' => '公告类型（1通知 2公告）']);
            $table->addColumn('text', 'content', ['length' => 1, 'comment' => '公告内容'])->nullable();
            $table->addColumn('integer', 'click_num', ['comment' => '浏览次数', 'default' => 0])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_notice');
    }
}
