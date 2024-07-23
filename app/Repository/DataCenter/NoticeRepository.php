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

namespace App\Repository\DataCenter;

use App\DataCenter\Model\Notice;
use Hyperf\Database\Model\Builder;
use App\Kernel\IRepository\AbstractRepository;

/**
 * 通知管理Mapper类.
 */
class NoticeRepository extends AbstractRepository
{
    /**
     * @var Notice
     */
    public $model;

    public function assignModel()
    {
        $this->model = Notice::class;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['title']) && filled($params['title'])) {
            $query->where('title', '=', $params['title']);
        }

        if (isset($params['type']) && filled($params['type'])) {
            $query->where('type', '=', $params['type']);
        }

        if (isset($params['created_at']) && filled($params['created_at']) && is_array($params['created_at']) && count($params['created_at']) == 2) {
            $query->whereBetween(
                'created_at',
                [$params['created_at'][0] . ' 00:00:00', $params['created_at'][1] . ' 23:59:59']
            );
        }

        return $query;
    }
}