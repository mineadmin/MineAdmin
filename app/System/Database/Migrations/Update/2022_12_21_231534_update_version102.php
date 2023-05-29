<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Hyperf\DbConnection\Db;

class UpdateVersion102 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 删除用户表 dept_id 字段
        Schema::table('system_user', function (Blueprint $table) {
            if (Schema::hasColumn('system_user','dept_id')) {
                $table->dropColumn(['dept_id']);
            }
        });

        // 新增用户部门中间表
        Schema::create('system_user_dept', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('用户与部门关联表');
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '用户主键']);
            $table->addColumn('bigInteger', 'dept_id', ['unsigned' => true, 'comment' => '部门主键']);
            $table->primary(['user_id', 'dept_id']);
        });

        // 新增部门领导表
        Schema::create('system_dept_leader', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('部门领导表');
            $table->addColumn('bigInteger', 'dept_id', ['unsigned' => true, 'comment' => '部门主键']);
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '用户主键']);
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->timestamp('created_at')->comment('添加时间');
            $table->primary(['dept_id', 'user_id']);
        });

        // 设置超管默认部门
        Db::table('system_user_dept')->insert([ 'user_id' => env('SUPER_ADMIN', 1), 'dept_id' => 1 ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_user', function (Blueprint $table) {
            if (Schema::hasColumn('system_user','dept_id')) {
                $table->addColumn('bigInteger', 'dept_id', ['unsigned' => true, 'comment' => '部门ID'])->nullable();
            }
        });
    }
}
