<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachment', function (Blueprint $table): void {
            $table->comment('上传文件信息表');
            $table->id()->comment('主键');
            $table->string('storage_mode', 20)->default('local')->comment('存储模式:local=本地,oss=阿里云,qiniu=七牛云,cos=腾讯云');
            $table->string('origin_name')->nullable()->comment('原文件名');
            $table->string('object_name', 50)->nullable()->comment('新文件名');
            $table->string('hash', 64)->nullable()->unique()->comment('文件hash');
            $table->string('mime_type')->nullable()->comment('资源类型');
            $table->string('storage_path', 100)->nullable()->index()->comment('存储目录');
            $table->string('suffix', 20)->nullable()->comment('文件后缀');
            $table->bigInteger('size_byte')->nullable()->comment('字节数');
            $table->string('size_info', 50)->nullable()->comment('文件大小');
            $table->string('url')->nullable()->comment('url地址');
            $table->unsignedBigInteger('created_by')->default(0)->comment('创建者');
            $table->unsignedBigInteger('updated_by')->default(0)->comment('更新者');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->string('remark')->default('')->comment('备注');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachment');
    }
};
