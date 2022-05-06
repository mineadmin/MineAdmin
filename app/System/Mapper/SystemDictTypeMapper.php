<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemDictData;
use App\System\Model\SystemDictType;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;
use Mine\Annotation\Transaction;

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

    /**
     * @param int $id
     * @param array $data
     * @return bool
     */
    #[Transaction]
    public function update(int $id, array $data): bool
    {
        parent::update($id, $data);
        SystemDictData::where('type_id', $id)->update(['code' => $data['code']]) > 0;
        return true;
    }

    /**
     * @param array $ids
     * @return bool
     */
    #[Transaction]
    public function realDelete(array $ids): bool
    {
        foreach ($ids as $id) {
            $model = $this->model::withTrashed()->find($id);
            if ($model) {
                $model->dictData()->forceDelete();
                $model->forceDelete();
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