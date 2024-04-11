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
use App\System\Model\SystemMenu;
use Hyperf\Database\Migrations\Migration;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Schema\Schema;

/**
 * 升级1.2.0版.
 */
class UpdateVersion120 extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('setting_datasource')) {
            Schema::create('setting_datasource', function (Blueprint $table) {
                $table->engine = 'Innodb';
                $table->comment('数据源表');
                $table->bigIncrements('id')->comment('主键');
                $table->addColumn('string', 'source_name', ['length' => 32, 'comment' => '数据源名称']);
                $table->addColumn('string', 'dsn', ['length' => 255, 'comment' => '连接dsn字符串'])->nullable();
                $table->addColumn('string', 'username', ['length' => 64, 'comment' => '数据库名称'])->nullable();
                $table->addColumn('string', 'password', ['length' => 32, 'comment' => '数据库用户'])->nullable();
                $table->addColumn('bigInteger', 'created_by', ['comment' => '创建者'])->nullable();
                $table->addColumn('bigInteger', 'updated_by', ['comment' => '更新者'])->nullable();
                $table->addColumn('timestamp', 'created_at', ['precision' => 0, 'comment' => '创建时间'])->nullable();
                $table->addColumn('timestamp', 'updated_at', ['precision' => 0, 'comment' => '更新时间'])->nullable();
                $table->addColumn('string', 'remark', ['length' => 255, 'comment' => '备注'])->nullable();
            });
        }

        // 菜单数据
        $pid = SystemMenu::create(
            [
                'parent_id' => 4000,
                'level' => '0,4000',
                'name' => '数据源管理',
                'code' => 'setting:datasource',
                'icon' => 'icon-storage',
                'route' => 'setting/datasource',
                'component' => 'setting/datasource/index',
                'redirect' => '',
                'is_hidden' => '2',
                'type' => 'M',
                'status' => '1',
                'sort' => 0,
                'created_by' => 1,
                'updated_by' => 0,
                'deleted_at' => null,
                'remark' => '',
            ]
        )->id;
        $menus = $this->menu($pid);

        foreach ($menus as $menu) {
            SystemMenu::create($menu);
        }
    }

    public function menu(int $pid): array
    {
        return [
            [
                'parent_id' => $pid,
                'level' => "0,4000,{$pid}",
                'name' => '数据源管理列表',
                'code' => 'setting:datasource:index',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0, // 假设创建者ID为0
                'updated_by' => 0, // 假设更新者ID为0

                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid, 'level' => "0,4000,{$pid}",
                'name' => '数据源管理保存',
                'code' => 'setting:datasource:save',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid, 'level' => "0,4000,{$pid}",
                'name' => '数据源管理更新',
                'code' => 'setting:datasource:update',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,
                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid, 'level' => "0,4000,{$pid}",
                'name' => '数据源管理读取',
                'code' => 'setting:datasource:read',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid, 'level' => "0,4000,{$pid}",
                'name' => '数据源管理删除',
                'code' => 'setting:datasource:delete',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid, 'level' => "0,4000,{$pid}",
                'name' => '数据源管理导入',
                'code' => 'setting:datasource:import',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
            [
                'parent_id' => $pid,
                'level' => "0,4000,{$pid}",
                'name' => '数据源管理导出',
                'code' => 'setting:datasource:export',
                'icon' => '',
                'route' => '',
                'component' => '',
                'redirect' => '',
                'is_hidden' => 2,
                'type' => 'B',
                'status' => 1,
                'sort' => 0,
                'created_by' => 0,
                'updated_by' => 0,

                'deleted_at' => null,
                'remark' => '',
            ],
        ];
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_datasource');
    }
}
