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

class CreateDeptLeaderTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dept_leader', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('部门领导表');
            $table->addColumn('bigInteger', 'dept_id', ['unsigned' => true, 'comment' => '部门主键']);
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '用户主键']);
            $table->addColumn('string', 'username', ['length' => 20, 'comment' => '用户名']);
            $table->timestamp('created_at')->comment('添加时间');
            $table->primary(['dept_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dept_leader');
    }
}
