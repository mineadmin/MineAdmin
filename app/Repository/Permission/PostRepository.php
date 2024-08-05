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

namespace App\Repository\Permission;

use App\Model\Permission\Post;
use App\Repository\IRepository;
use Hyperf\Database\Model\Builder;

class PostRepository extends IRepository
{
    public function __construct(
        protected readonly Post $model
    ) {}

    public function handleSearch(Builder $query, array $params): Builder
    {
        return $query;
    }
}
