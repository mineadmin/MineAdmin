<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

/**
 * 升级1.2.0版
 */
class UpdateVersion120 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_datasource', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('数据源表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('string', 'name', ['length' => 32, 'comment' => '数据源名称']);
            $table->addColumn('string', 'db_host', ['length'=> 32, 'comment' => '数据库地址'])->nullable();
            $table->addColumn('string', 'db_port', ['length'=> 12, 'comment' => '数据库端口'])->nullable();
            $table->addColumn('string', 'db_name', ['length'=> 64, 'comment' => '数据库名称'])->nullable();
            $table->addColumn('string', 'db_user', ['length'=> 32, 'comment' => '数据库用户'])->nullable();
            $table->addColumn('string', 'db_pass', ['length'=> 32, 'comment' => '数据库密码'])->nullable();
            $table->addColumn('string', 'db_charset', ['length'=> 32, 'comment' => '数据库字符集'])->nullable();
            $table->addColumn('string', 'db_collation', ['length'=> 32, 'comment' => '数据库字符序'])->nullable();
            $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
            $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
            $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
            $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
            $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_datasource');
    }
}
