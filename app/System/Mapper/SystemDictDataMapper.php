<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemDictData;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemUserMapper
 * @package App\System\Mapper
 */
class SystemDictDataMapper extends AbstractMapper
{
    /**
     * @var SystemDictData
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemDictData::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['type_id'])) {
            $query->where('type_id', $params['type_id']);
        }
        if (isset($params['code'])) {
            $query->where('code', $params['code']);
        }
        if (isset($params['value'])) {
            $query->where('value', 'like', '%'.$params['value'].'%');
        }
        if (isset($params['label'])) {
            $query->where('label', 'like', '%'.$params['label'].'%');
        }
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        return $query;
    }
}