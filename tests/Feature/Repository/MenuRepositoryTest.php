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

use App\Model\Enums\User\Status;
use App\Model\Permission\Meta;
use App\Repository\Permission\MenuRepository;
use Hyperf\Collection\Collection;
use Hyperf\DbConnection\Model\Model;
use Hyperf\Stringable\Str;

/**
 * @internal
 * @coversNothing
 */
final class MenuRepositoryTest extends AbstractTestRepository
{
    protected string $repositoryClass = MenuRepository::class;

    protected function getAttributes(): array
    {
        return [
            'parent_id' => random_int(1, 1000000),
            'name' => Str::random(),
            'component' => Str::random(),
            'redirect' => Str::random(),
            'path' => Str::random(),
            'status' => Status::Normal,
            'meta' => new Meta(),
            'sort' => random_int(1, 99),
            'created_by' => random_int(1, 1000000),
            'updated_by' => random_int(1, 1000000),
        ];
    }

    protected function getSearchAttributes(Model $model, Collection $entityList): array
    {
        return [
            'sortable' => ['id' => 'desc'],
            'code' => $entityList->pluck('name')->toArray(),
            'name' => $entityList->pluck('name')->toArray(),
            'children' => true,
            'status' => Status::Normal,
            'parent_id' => $model->parent_id,
        ];
    }
}
