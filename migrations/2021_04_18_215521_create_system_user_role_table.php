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

class CreateSystemUserRoleTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_user_role', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('用户与角色关联表');
            $table->addColumn('bigInteger', 'user_id', ['unsigned' => true, 'comment' => '用户主键']);
            $table->addColumn('bigInteger', 'role_id', ['unsigned' => true, 'comment' => '角色主键']);
            $table->primary(['user_id', 'role_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_user_role');
    }
}
