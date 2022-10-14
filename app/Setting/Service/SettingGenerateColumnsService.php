<?php

declare(strict_types=1);
namespace App\Setting\Service;

use App\Setting\Mapper\SettingGenerateColumnsMapper;
use App\Setting\Model\SettingGenerateColumns;
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
                'is_pk' => empty($item['column_key']) ? SettingGenerateColumns::NO : SettingGenerateColumns::YES ,
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
                    [
                        'is_insert' => SettingGenerateColumns::YES,
                        'is_edit' => SettingGenerateColumns::YES,
                        'is_list' => SettingGenerateColumns::YES,
                        'is_query' => SettingGenerateColumns::YES,
                        'is_sort' => SettingGenerateColumns::NO,
                    ]
                );
            }

            $this->mapper->save($column);
        }
        return 1;
    }

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $data['is_insert'] = $data['is_insert'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        $data['is_edit'] = $data['is_edit'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        $data['is_list'] = $data['is_list'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        $data['is_query'] = $data['is_query'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        $data['is_sort'] = $data['is_sort'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        $data['is_required'] = $data['is_required'] ? SettingGenerateColumns::YES : SettingGenerateColumns::NO;
        return $this->mapper->update($id, $data);
    }
}