<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\System\Mapper;

use App\System\Model\SystemApi;
use App\System\Model\SystemApiGroup;
use Hyperf\Database\Model\Builder;
use Mine\Abstracts\AbstractMapper;

/**
 * Class SystemApiGroupMapper.
 */
class SystemApiGroupMapper extends AbstractMapper
{
    /**
     * @var SystemApiGroup
     */
    public $model;

    public function assignModel()
    {
        $this->model = SystemApiGroup::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        // 应用组名称
        if (! empty($params['name'])) {
            $query->where('name', '=', $params['name']);
        }

        // 状态
        if (! empty($params['status'])) {
            $query->where('status', '=', $params['status']);
        }

        // 关联查询api列表
        if (! empty($params['getApiList']) && $params['getApiList'] == true) {
            $query->with(['apis' => function ($query) {
                $query->where('status', SystemApi::ENABLE)->select(['id', 'group_id', 'name', 'access_name']);
            }]);
        }
        return $query;
    }
}
