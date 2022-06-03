<?php

declare (strict_types=1);
namespace App\Setting\Model;

use Mine\MineModel;
/**
 * @property int $id 主键
 * @property int $table_id 所属表ID
 * @property string $column_name 字段名称
 * @property string $column_comment 字段注释
 * @property string $column_type 字段类型
 * @property string $is_pk 0 非主键 1 主键
 * @property string $is_required 0 非必填 1 必填
 * @property string $is_insert 0 非插入字段 1 插入字段
 * @property string $is_edit 0 非编辑字段 1 编辑字段
 * @property string $is_list 0 非列表显示字段 1 列表显示字段
 * @property string $is_query 0 非查询字段 1 查询字段
 * @property string $query_type 查询方式 eq 等于, neq 不等于, gt 大于, lt 小于, like 范围
 * @property string $view_type 页面控件，text, textarea, password, select, checkbox, radio, date, upload, ma-upload（封装的上传控件）
 * @property string $dict_type 字典类型
 * @property string $allow_roles 允许查看该字段的角色
 * @property string $options 字段其他设置
 * @property int $sort 排序
 * @property int $created_by 创建者
 * @property int $updated_by 更新者
 * @property \Carbon\Carbon $created_at 创建时间
 * @property \Carbon\Carbon $updated_at 更新时间
 * @property string $remark 备注
 */
class SettingGenerateColumns extends MineModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'setting_generate_columns';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'table_id', 'column_name', 'column_comment', 'column_type', 'is_pk', 'is_required', 'is_insert', 'is_edit', 'is_list', 'is_query', 'query_type', 'view_type', 'dict_type', 'allow_roles', 'options', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'table_id' => 'integer', 'sort' => 'integer', 'created_by' => 'integer', 'updated_by' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'options' => 'array'];
}