<?php

declare(strict_types=1);
namespace App\Setting\Mapper;

use App\Setting\Model\SettingCrontab;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

class SettingCrontabMapper extends AbstractMapper
{
    /**
     * @var SettingCrontab
     */
    public $model;

    public function assignModel()
    {
        $this->model = SettingCrontab::class;
    }

    public function delete(array $ids): bool
    {
        try {
            Db::beginTransaction();
            foreach ($ids as $id) {
                $model = $this->model::find($id);
                $model->logs()->delete();
                $model->delete();
            }
            Db::commit();
        } catch (\RuntimeException $e) {
            Db::rollBack();
            return false;
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
        if (isset($params['name'])) {
            $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        if (isset($params['type'])) {
            $query->where('type', $params['type']);
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