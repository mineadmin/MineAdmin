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

namespace App\Repository\Tools;

use App\Model\Tools\GenerateTables;
use Hyperf\Database\Model\Builder;
use App\Kernel\IRepository\AbstractRepository;
use Mine\Annotation\Transaction;

/**
 * 生成业务信息表查询类
 * class GenerateTablesRepository.
 */
class GenerateTablesRepository extends AbstractRepository
{
    /**
     * @var GenerateTables
     */
    public $model;

    public function assignModel()
    {
        $this->model = GenerateTables::class;
    }

    /**
     * 删除业务信息表和字段信息表.
     * @throws \Exception
     */
    #[Transaction]
    public function delete(array $ids): bool
    {
        /**
         * @var GenerateTables $model
         */
        foreach ($this->model::query()->whereIn('id', $ids)->get() as $model) {
            if ($model) {
                $model->columns()->delete();
                $model->delete();
            }
        }
        return true;
    }

    /**
     * 搜索处理器.
     */
    public function handleSearch(Builder $query, array $params): Builder
    {
        if (isset($params['table_name']) && filled($params['table_name'])) {
            $query->where('table_name', 'like', '%' . $params['table_name'] . '%');
        }
        if (isset($params['minDate']) && filled($params['minDate']) && isset($params['maxDate']) && filled($params['maxDate'])) {
            $query->whereBetween(
                'created_at',
                [$params['minDate'] . ' 00:00:00', $params['maxDate'] . ' 23:59:59']
            );
        }
        return $query;
    }
}
