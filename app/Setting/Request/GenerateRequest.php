<?php
declare(strict_types=1);
namespace App\Setting\Request;

use Mine\MineFormRequest;

class GenerateRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function updateRules(): array
    {
        return [
            'id' => 'required',
            'generate_type' => 'required',
            'build_menu' => 'required',
            'generate_menus' => 'array',
            'menu_name' => 'required',
            'module_name' => 'required',
            'table_comment' => 'required',
            'table_name' => 'required',
            'type' => 'required',
            'component_type' => 'required',
            'columns' => 'required|array',
            'package_name' => '',
            'belong_menu_id' => '',
            'options' => '',
            'remark' => '',
        ];
    }

    public function loadTableRules(): array
    {
        return [
            'names' => 'required|array',
        ];
    }

    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'id' => '业务表ID',
            'generate_type' => '生成类型',
            'build_menu' => '是否构建菜单',
            'generate_menus' => '生成菜单列表',
            'menu_name' => '菜单名称',
            'module_name' => '模块名称',
            'table_comment' => '业务表说明',
            'table_name' => '业务表名称',
            'type' => '生成类型',
            'component_type' => '组件类型',
            'columns' => '字段列表',
            'names' => '业务表名称组'
        ];
    }
}