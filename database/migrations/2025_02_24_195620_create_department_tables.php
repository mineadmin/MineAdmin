<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('department', function (Blueprint $table): void {
            $table->comment('部门表');
            $table->id()->comment('主键');
            $table->string('name', 50)->comment('部门名称');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('父级部门ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('position', function (Blueprint $table): void {
            $table->comment('岗位表');
            $table->id()->comment('主键');
            $table->string('name', 50)->comment('岗位名称');
            $table->unsignedBigInteger('dept_id')->comment('部门ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('user_dept', function (Blueprint $table): void {
            $table->comment('用户-部门关联表');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('dept_id')->comment('部门ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('user_position', function (Blueprint $table): void {
            $table->comment('用户-岗位关联表');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->unsignedBigInteger('position_id')->comment('岗位ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('dept_leader', function (Blueprint $table): void {
            $table->comment('部门领导表');
            $table->unsignedBigInteger('dept_id')->comment('部门ID');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('data_permission_policy', function (Blueprint $table): void {
            $table->comment('数据权限策略');
            $table->id()->comment('主键');
            $table->unsignedBigInteger('user_id')->default(0)->comment('用户ID（与角色二选一）');
            $table->unsignedBigInteger('position_id')->default(0)->comment('岗位ID（与用户二选一）');
            $table->string('policy_type', 20)->comment('策略类型（DEPT_SELF, DEPT_TREE, ALL, SELF, CUSTOM_DEPT, CUSTOM_FUNC）');
            $table->boolean('is_default')->default(true)->comment('是否默认策略（默认值：true）');
            $table->json('value')->nullable()->comment('策略值');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
            $table->softDeletes()->comment('删除时间');
        });

        Schema::create('data_permission_policy_position', function (Blueprint $table): void {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('policy_id')->comment('策略ID');
            $table->unsignedBigInteger('position_id')->comment('岗位ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });

        Schema::create('data_permission_policy_user', function (Blueprint $table): void {
            $table->id()->comment('主键');
            $table->unsignedBigInteger('policy_id')->comment('策略ID');
            $table->unsignedBigInteger('user_id')->comment('用户ID');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->timestamp('updated_at')->nullable()->comment('更新时间');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_permission_policy_user');
        Schema::dropIfExists('data_permission_policy_position');
        Schema::dropIfExists('data_permission_policy');
        Schema::dropIfExists('dept_leader');
        Schema::dropIfExists('user_position');
        Schema::dropIfExists('user_dept');
        Schema::dropIfExists('position');
        Schema::dropIfExists('department');
    }
};
