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

class CreateSystemRoleMenuTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_role_menu', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('角色与菜单关联表');
            $table->addColumn('bigInteger', 'role_id', ['unsigned' => true, 'comment' => '角色主键']);
            $table->addColumn('bigInteger', 'menu_id', ['unsigned' => true, 'comment' => '菜单主键']);
            $table->primary(['role_id', 'menu_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_role_menu');
    }
}
