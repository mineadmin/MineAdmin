<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 租户表
        Schema::create('code_generator', function (Blueprint $table) {
            $table->comment('代码生成表');
            $table->bigIncrements('id')->comment('ID');
            $table->authorBy();
            $table->datetimes();
            $table->string('remark', 255)->default('')->comment('备注');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('code_generator');
    }
};
