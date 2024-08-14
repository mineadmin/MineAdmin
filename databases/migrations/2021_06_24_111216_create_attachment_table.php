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

class CreateAttachmentTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attachment', function (Blueprint $table) {
            
            $table->comment('上传文件信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'storage_mode', ['length'=>20,'comment' => '存储模式 (1 本地 2 阿里云 3 七牛云 4 腾讯云)'])->default('local');
            $table->addColumn('string', 'origin_name', ['length' => 255, 'comment' => '原文件名'])->nullable();
            $table->addColumn('string', 'object_name', ['length' => 50, 'comment' => '新文件名'])->nullable();
            $table->addColumn('string', 'hash', ['length' => 64, 'comment' => '文件hash'])->nullable();
            $table->addColumn('string', 'mime_type', ['length' => 255, 'comment' => '资源类型'])->nullable();
            $table->addColumn('string', 'storage_path', ['length' => 100, 'comment' => '存储目录'])->nullable();
            $table->addColumn('string', 'suffix', ['length' => 10, 'comment' => '文件后缀'])->nullable();
            $table->addColumn('bigInteger', 'size_byte', ['length' => 20, 'comment' => '字节数'])->nullable();
            $table->addColumn('string', 'size_info', ['length' => 50, 'comment' => '文件大小'])->nullable();
            $table->addColumn('string', 'url', ['length' => 255, 'comment' => 'url地址'])->nullable();
            $table->bigInteger('created_by')->comment('创建者')->default(0);
            $table->bigInteger('updated_by')->comment('更新者')->default(0);
            $table->datetimes();
            $table->softDeletes();
            $table->string('remark')->comment('备注')->default('');
            $table->index('storage_path');
            $table->unique('hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uploadfile');
    }
}
