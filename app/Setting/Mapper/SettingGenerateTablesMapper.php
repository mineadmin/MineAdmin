<?php

declare(strict_types=1);
namespace App\Setting\Mapper;

use App\Setting\Model\SettingGenerateTables;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

/**
 * 生成业务信息表查询类
 * Class SettingGenerateTablesMapper
 * @package App\Setting\Mapper
 */
class SettingGenerateTablesMapper extends AbstractMapper
{
    /**
     * @var SettingGenerateTables
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingGenerateTables::class;
    }

    /**
     * 删除业务信息表和字段信息表
     * @throws \Exception
     */
    #[Transaction]
    public function delete(array $ids): bool
    {
        /* @var SettingGenerateTables $model */
        foreach ($this->model::query()->whereIn('id', $ids)->get() as $model) {
            if ($model) {
                $model->columns()->delete();
                $model->delete();
            }
        }
        return true;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['table_name'])) {
            $query->where('table_name', 'like', '%'.$params['table_name'].'%');
        }
        if (isset($params['minDate']) && isset($params['maxDate'])) {
            $query->whereBetween(
                'created_at',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }
}