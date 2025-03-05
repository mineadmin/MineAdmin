<?php

use App\Model\Permission\Menu;
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
        Schema::dropIfExists('department');
        Schema::dropIfExists('position');
        Schema::dropIfExists('user_dept');
        Schema::dropIfExists('user_position');
        Schema::dropIfExists('dept_leader');
        Schema::dropIfExists('data_permission_policy');
        Schema::create('department', function (Blueprint $table) {
            $table->comment('部门表');
            $table->bigIncrements('id');
            $table->string('name', 50)->comment('部门名称');
            $table->bigInteger('parent_id')->default(0)->comment('父级部门ID');
            $table->datetimes();
            $table->softDeletes();
        });
        Schema::create('position', function (Blueprint $table) {
            $table->comment('岗位表');
            $table->bigIncrements('id');
            $table->string('name', 50)->comment('岗位名称');
            $table->bigInteger('dept_id')->comment('部门ID');
            $table->datetimes();
            $table->softDeletes();
        });
        Schema::create('user_dept', function (Blueprint $table) {
            $table->comment('用户-部门关联表');
            $table->bigInteger('user_id');
            $table->bigInteger('dept_id');
            $table->datetimes();
            $table->softDeletes();
        });
        Schema::create('user_position', function (Blueprint $table) {
            $table->comment('用户-岗位关联表');
            $table->bigInteger('user_id');
            $table->bigInteger('position_id');
            $table->datetimes();
            $table->softDeletes();
        });
        Schema::create('dept_leader', function (Blueprint $table) {
            $table->comment('部门领导表');
            $table->bigInteger('dept_id');
            $table->bigInteger('user_id');
            $table->datetimes();
            $table->softDeletes();
        });
        Schema::create('data_permission_policy', function (Blueprint $table) {
            $table->comment('数据权限策略');
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->default(0)->comment('用户ID（与角色二选一）');
            $table->bigInteger('role_id')->default(0)->comment('角色ID（与用户二选一）');
            $table->string('policy_type', 20)->comment('策略类型（DEPT_SELF, DEPT_TREE, ALL, SELF, CUSTOM_DEPT, CUSTOM_FUNC）');
            $table->boolean('is_default')->default(true)->comment('是否默认策略（默认值：true）');
            $table->json('value')->nullable()->comment('策略值');
            $table->datetimes();
            $table->softDeletes();
        });
        $parent = Menu::where('name','permission')->firstOrFail();
        $now = Menu::create([
            'name'  =>  'permission:department',
            'path'  =>  '/permission/departments',
            'parent_id' => $parent->id,
            'meta'  =>  [
                'title' => '部门管理',
                'icon' => 'ri:git-repository-private-line',
                'type' => 'M',
                'hidden' => 0,
                'componentPath' => 'modules/',
                'componentSuffix' => '.vue',
                'breadcrumbEnable' => 1,
                'copyright' => 1,
                'cache' => 1,
                'affix' => 0,
            ]
        ]);
        $children = [
            'permission:department:index' => '部门列表',
            'permission:department:save'    =>  '部门新增',
            'permission:department:update'  =>  '部门编辑',
            'permission:department:delete'  =>  '部门删除',
        ];
        foreach ($children as $child => $title) {
            Menu::create([
                'name'  =>  $child,
                'path'  =>  '/permission/departments',
                'meta'  =>  [
                    'title' => $title,
                    'icon' => 'ri:git-repository-private-line',
                    'type' => 'M',
                    'hidden' => 1,
                    'componentPath' => 'modules/',
                    'componentSuffix' => '.vue',
                    'breadcrumbEnable' => 1,
                    'cache' => 1,
                    'affix' => 0,
                ],
                'parent_id' => $now->id
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department');
        Schema::dropIfExists('position');
        Schema::dropIfExists('user_dept');
        Schema::dropIfExists('user_position');
        Schema::dropIfExists('dept_leader');
        Schema::dropIfExists('data_permission_policy');
    }
};
