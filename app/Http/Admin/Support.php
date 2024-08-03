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

namespace App\Http\Admin\Support;

use App\Http\Admin\CurrentUser;
use App\Model\Permission\User;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;

function user(): ?User
{
    return ApplicationContext::getContainer()->get(CurrentUser::class)->user();
}

function data_to_tree(array|Collection $data, int $parentId = 0, string $id = 'id', string $parentField = 'parent_id', string $children = 'children'): Collection
{
    $data = $data instanceof Collection ? $data : Collection::make($data);
    if ($data->isEmpty()) {
        return Collection::make();
    }
    $tree = Collection::make();
    foreach ($data as $item) {
        if ($item[$parentField] === $parentId) {
            $item[$children] = data_to_tree($data, $item[$id], $id, $parentField, $children);
            $tree->push($item);
        }
    }
    return $tree;
}
