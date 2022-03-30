<?php

declare(strict_types=1);
namespace App\Setting\Mapper;

use App\Setting\Model\SettingGenerateColumns;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * 生成业务字段信息表查询类
 * Class SettingGenerateColumnsMapper
 * @package App\Setting\Mapper
 */
class SettingGenerateColumnsMapper extends AbstractMapper
{
    /**
     * @var SettingGenerateColumns
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingGenerateColumns::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if ($params['table_id'] ?? false) {
            $query->where('table_id', (int) $params['table_id']);
        }
        return $query;
    }
}