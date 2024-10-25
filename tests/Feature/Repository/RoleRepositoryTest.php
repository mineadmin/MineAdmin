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

namespace HyperfTests\Feature\Repository;

use App\Model\Permission\Role;
use App\Repository\Permission\RoleRepository;
use Carbon\Carbon;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class RoleRepositoryTest extends AbstractTestRepository
{
    protected string $repositoryClass = RoleRepository::class;

    protected function getAttributes(): array
    {
        /*
         * @property string $name 角色名称
         * @property string $code 角色代码
         * @property int $status 状态 (1正常 2停用)
         * @property int $sort 排序
         * @property int $created_by 创建者
         * @property int $updated_by 更新者
         * @property Carbon $created_at 创建时间
         * @property Carbon $updated_at 更新时间
         * @property string $remark 备注
         */
        return [
            'name' => Str::random(),
            'code' => Str::random(),
            'status' => 1,
            'sort' => 1,
            'created_by' => 1,
            'updated_by' => 1,
        ];
    }

    /**
     * @param Role $model
     */
    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'name' => $model->name,
            'code' => $model->code,
            'status' => $model->status,
            'created_at' => [$model->created_at->startOfDay(), $model->created_at->endOfDay()],
        ];
    }
}
