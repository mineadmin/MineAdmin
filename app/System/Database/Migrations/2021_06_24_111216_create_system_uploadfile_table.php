<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSystemUploadfileTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_uploadfile', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('上传文件信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('char', 'storage_mode', ['length' => 1, 'default' => '1', 'comment' => '状态 (1 本地 2 阿里云 3 七牛云 4 腾讯云)'])->nullable();
            $table->addColumn('string', 'origin_name', ['length' => 255, 'comment' => '原文件名'])->nullable();
            $table->addColumn('string', 'object_name', ['length' => 50, 'comment' => '新文件名'])->nullable();
            $table->addColumn('string', 'mime_type', ['length' => 255, 'comment' => '资源类型'])->nullable();
            $table->addColumn('string', 'storage_path', ['length' => 100, 'comment' => '存储目录'])->nullable();
            $table->addColumn('string', 'suffix', ['length' => 10, 'comment' => '文件后缀'])->nullable();
            $table->addColumn('bigInteger', 'size_byte', ['length' => 20, 'comment' => '字节数'])->nullable();
            $table->addColumn('string', 'size_info', ['length' => 50, 'comment' => '文件大小'])->nullable();
            $table->addColumn('string', 'url', ['length' => 255, 'comment' => 'url地址'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('timestamp', 'deleted_at', ['precision' => 0, 'comment' => '删除时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            $table->index('storage_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_uploadfile');
    }
}
