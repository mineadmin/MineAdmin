<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemDictData;
use App\System\Model\SystemDictType;
use Hyperf\Database\Model\Builder;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemUserMapper
 * @package App\System\Mapper
 */
class SystemDictTypeMapper extends AbstractMapper
{
    /**
     * @var SystemDictType
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemDictType::class;
    }

    public function update(int $id, array $data): bool
    {
        try {
            Db::beginTransaction();
            parent::update($id, $data);
            SystemDictData::where('type_id', $id)->update(['code' => $data['code']]) > 0;
            Db::commit();
        } catch (\RuntimeException $e) {
            Db::rollBack();
            return false;
        }
        return true;
    }

    public function realDelete(array $ids): bool
    {
        try {
            Db::beginTransaction();
            foreach ($ids as $id) {
                $model = $this->model::withTrashed()->find($id);
                $model->dictData()->forceDelete();
                $model->forceDelete();
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
        if (isset($params['code'])) {
            $query->where('code', 'like', '%'.$params['code'].'%');
        }
        if (isset($params['name'])) {
            $query->where('name', 'like', '%'.$params['name'].'%');
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }
        return $query;
    }
}