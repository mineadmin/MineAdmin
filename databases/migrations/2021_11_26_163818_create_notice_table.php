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

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notice', function (Blueprint $table) {
            
            $table->comment('系统公告表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'message_id')->comment('消息ID');
            $table->addColumn('string', 'title', ['length' => 255, 'comment' => '标题']);
            $table->addColumn('smallInteger', 'type', ['comment' => '公告类型（1通知 2公告）']);
            $table->addColumn('text', 'content', ['length' => 1, 'comment' => '公告内容'])->nullable();
            $table->addColumn('integer', 'click_num', ['comment' => '浏览次数', 'default' => 0])->nullable();
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->index('message_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notice');
    }
}
