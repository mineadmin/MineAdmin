<?php

declare(strict_types=1);
namespace App\Setting\Service;

use App\Setting\Mapper\SettingGenerateColumnsMapper;
use App\Setting\Model\SettingGenerateColumns;
use Mine\Abstracts\AbstractService;
use function PHPStan\dumpType;

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
                'is_required' => $item['is_nullable'] == 'NO' ? SettingGenerateColumns::YES : SettingGenerateColumns::NO,
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

            $this->mapper->save(
                $this->fieldDispose($column));
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

    private function fieldDispose(array $column): array
    {
        $object = new class {
            public function viewTypeDispose(&$column): void
            {
                switch ($column['column_type']) {
                    case 'varchar':
                        $column['query_type'] = 'like';
                        $column['view_type'] = 'text';
                        break;
                    // 富文本
                    case 'text':
                    case 'longtext':
                        $column['is_list'] = SettingGenerateColumns::NO;
                        $column['is_query'] = SettingGenerateColumns::NO;
                        $column['view_type'] = 'editor';
                        break;
                    // 日期字段
                    case 'timestamp':
                    case 'datetime':
                        $column['view_type'] = 'date';
                        $column['options']['mode'] = 'date';
                        $column['options']['showTime'] = true;
                        $column['query_type'] = 'between';
                    break;
                    case 'date':
                        $column['view_type'] = 'date';
                        $column['options']['mode'] = 'date';
                        $column['options']['showTime'] = false;
                        $column['query_type'] = 'between';
                        break;
                    case 'json':
                        $column['is_list'] = SettingGenerateColumns::NO;
                        $column['is_query'] = SettingGenerateColumns::NO;
                        break;
                }
            }
            public function columnCommentDispose(&$column): void
            {
                if (preg_match('/.*:.*=.*/m', $column['column_comment'])) {
                    $regs = explode(':', $column['column_comment']);
                    $column['column_comment'] = $regs[0] ?? '';
                    $column['view_type'] = 'select';
                    $column['options']['collection'] = array_map(function ($item) {
                        $item = explode('=', $item);
                        return [
                            'label' => $item[1] ?? '',
                            'value' => $item[0] ?? ''
                        ];
                    }, explode(',', $regs[1] ?? ''));
                }
            }
            public function columnName(&$column): void
            {
                if (stristr($column['column_name'], 'image')) {
                    $column['is_query'] = SettingGenerateColumns::NO;
                    $column['view_type'] = 'upload';
                    $column['options']['type'] = 'image';
                    $column['options']['multiple'] = false;
                    $column['query_type'] = 'eq';
                }

                if (stristr($column['column_name'], 'images')) {
                    $column['is_query'] = SettingGenerateColumns::NO;
                    $column['view_type'] = 'upload';
                    $column['options']['type'] = 'image';
                    $column['options']['multiple'] = true;
                    $column['query_type'] = 'eq';
                }

                if (stristr($column['column_name'], 'file')) {
                    $column['is_query'] = SettingGenerateColumns::NO;
                    $column['view_type'] = 'upload';
                    $column['options']['type'] = 'file';
                    $column['options']['multiple'] = false;
                    $column['query_type'] = 'eq';
                }

                if (stristr($column['column_name'], 'files')) {
                    $column['is_query'] = SettingGenerateColumns::NO;
                    $column['view_type'] = 'upload';
                    $column['options']['type'] = 'file';
                    $column['options']['multiple'] = true;
                    $column['query_type'] = 'eq';
                }
            }
        };

        $object->viewTypeDispose($column);
        $object->columnCommentDispose($column);
        $object->columnName($column);
        return $column;
    }
}