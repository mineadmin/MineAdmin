<?php

declare(strict_types=1);
namespace App\Setting\Service;

use App\Setting\Mapper\SettingGenerateColumnsMapper;
use Mine\Abstracts\AbstractService;

/**
 * 业务生成字段信息表业务处理类
 * Class SettingGenerateColumnsService
 * @package App\Setting\Service
 */
class SettingGenerateColumnsService extends AbstractService
{
    /**
     * @var SettingGenerateColumnsMapper
     */
    public $mapper;

    /**
     * SettingGenerateColumnsService constructor.
     * @param SettingGenerateColumnsMapper $mapper
     */
    public function __construct(SettingGenerateColumnsMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 循环插入数据
     * @param array $data
     * @return int
     */
    public function save(array $data): int
    {
        $default_column = ['created_at', 'updated_at', 'created_by', 'updated_by', 'deleted_at', 'remark'];
        // 组装数据
        foreach ($data as $k => $item) {

            $column = [
                'table_id' => $item['table_id'],
                'column_name' => $item['column_name'],
                'column_comment' => $item['column_comment'],
                'column_type' => $item['data_type'],
                'is_pk' => empty($item['column_key']) ? 1 : 2 ,
                'query_type' => 'eq',
                'view_type' => 'text',
                'sort' => count($data) - $k,
                'allow_roles' => $item['allow_roles'] ?? null,
                'options' => $item['options'] ?? null
            ];

            // 设置默认选项
            if (!in_array($item['column_name'], $default_column) && empty($item['column_key'])) {
                $column = array_merge(
                    $column,
                    ['is_insert' => 2, 'is_edit' => 2, 'is_list' => 2, 'is_query' => 2]
                );
            }

            $this->mapper->save($column);
        }
        return 1;
    }
}