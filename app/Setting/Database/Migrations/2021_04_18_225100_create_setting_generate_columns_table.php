<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

class CreateSettingGenerateColumnsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('setting_generate_columns', function (Blueprint $table) {
            $table->engine = 'Innodb';
            $table->comment('代码生成业务字段信息表');
            $table->bigIncrements('id')->comment('主键');
            $table->addColumn('bigInteger', 'table_id', ['unsigned' => true, 'comment' => '所属表ID']);
            $table->addColumn('string', 'column_name', ['length' => 200, 'comment' => '字段名称'])->nullable();
            $table->addColumn('string', 'column_comment', ['length' => 255, 'comment' => '字段注释'])->nullable();
            $table->addColumn('string', 'column_type', ['length' => 50, 'comment' => '字段类型'])->nullable();
            $table->addColumn('char', 'is_pk', ['length' => 1, 'default' => '0', 'comment' => '0 非主键 1 主键'])->nullable();
            $table->addColumn('char', 'is_required', ['length' => 1, 'default' => '0', 'comment' => '0 非必填 1 必填'])->nullable();
            $table->addColumn('char', 'is_insert', ['length' => 1, 'default' => '0', 'comment' => '0 非插入字段 1 插入字段'])->nullable();
            $table->addColumn('char', 'is_edit', ['length' => 1, 'default' => '0', 'comment' => '0 非编辑字段 1 编辑字段'])->nullable();
            $table->addColumn('char', 'is_list', ['length' => 1, 'default' => '0', 'comment' => '0 非列表显示字段 1 列表显示字段'])->nullable();
            $table->addColumn('char', 'is_query', ['length' => 1, 'default' => '0', 'comment' => '0 非查询字段 1 查询字段'])->nullable();
            $table->addColumn(
                'string', 'query_type',
                [
                    'length' => 100,
                    'default' => 'eq',
                    'comment' => '查询方式 eq 等于, neq 不等于, gt 大于, lt 小于, like 范围'
                ]
            )->nullable();
            $table->addColumn(
                'string', 'view_type',
                [
                    'length' => 100,
                    'default' => 'text',
                    'comment' => '页面控件，text, textarea, password, select, checkbox, radio, date, upload, ma-upload（封装的上传控件）'
                ]
            )->nullable();
            $table->addColumn('string', 'dict_type', ['length' => 200, 'comment' => '字典类型'])->nullable();
            $table->addColumn('string', 'allow_roles', ['length' => 255, 'comment' => '允许查看该字段的角色'])->nullable();
            $table->addColumn('string', 'options', ['length' => 1000, 'comment' => '字段其他设置'])->nullable();
            $table->addColumn('tinyInteger', 'sort', ['unsigned' => true, 'default' => 0, 'comment' => '排序'])->nullable();
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
        Schema::dropIfExists('setting_generate_columns');
    }
}
