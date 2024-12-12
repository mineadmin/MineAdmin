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
        Schema::create('attachment', static function (Blueprint $table) {
            $table->comment('上传文件信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->string('storage_mode', 20)->comment('存储模式:local=本地,oss=阿里云,qiniu=七牛云,cos=腾讯云')->default('local');
            $table->string('origin_name', 255)->comment('原文件名')->nullable();
            $table->string('object_name', 50)->comment('新文件名')->nullable();
            $table->string('hash', 64)->comment('文件hash')->nullable();
            $table->string('mime_type', 255)->comment('资源类型')->nullable();
            $table->string('storage_path', 100)->comment('存储目录')->nullable();
            $table->string('suffix', 20)->comment('文件后缀')->nullable();
            $table->bigInteger('size_byte')->comment('字节数')->nullable();
            $table->string('size_info', 50)->comment('文件大小')->nullable();
            $table->string('url', 255)->comment('url地址')->nullable();
            $table->authorBy();
            $table->datetimes();
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
        Schema::dropIfExists('attachment');
    }
}
