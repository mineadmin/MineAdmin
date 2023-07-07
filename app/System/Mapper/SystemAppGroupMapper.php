<?php
declare(strict_types=1);
namespace App\System\Mapper;

use App\System\Model\SystemAppGroup;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemAppGroupMapper
 * @package App\System\Mapper
 */
class SystemAppGroupMapper extends AbstractMapper
{
    /**
     * @var SystemAppGroup
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemAppGroup::class;
    }

    /**
     * 搜索处理器
     * @param Builder $query
     * @param array $params
     * @return Builder
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 应用组名称
        if (!empty($params['name'])) {
            $query->where('name', '=', $params['name']);
        }

        // 状态
        if (!empty($params['status'])) {
            $query->where('status', '=', $params['status']);
        }
        return $query;
    }
}