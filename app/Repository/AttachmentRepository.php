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

namespace App\Repository;

use App\Model\Attachment;
use Hyperf\Collection\Arr;
use Hyperf\Database\Model\Builder;

/**
 * @extends IRepository<Attachment>
 */
final class AttachmentRepository extends IRepository
{
    public function __construct(
        protected readonly Attachment $model
    ) {}

    public function findByHash(string $hash): ?Attachment
    {
        return $this->model->newQuery()->where('hash', $hash)->first();
    }

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query->when(Arr::get($params, 'suffix'), function (Builder $query, $suffix) {
            $suffix = is_array($suffix) ? $suffix : explode(',', $suffix);
            $query->whereIn('suffix', $suffix);
        });
    }
}
