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

namespace App\Model\Scopes;

use App\Http\CurrentUser;
use App\Model\DataPermission\Policy;
use App\Model\DataScope;
use App\Model\Enums\DataPermission\PolicyType;
use App\Model\Permission\Department;
use Hyperf\Database\Model\Builder;
use Hyperf\Database\Model\Model;
use Hyperf\Database\Model\Scope;
use Hyperf\Database\Schema\Schema;

class DataScopeScope implements Scope
{
    /**
     * 应用作用域
     */
    public function apply(Builder $builder, Model $model): void
    {
        $table = $model->getTable();

        if ($model->getDataScope()) {
            DataScope::query($builder, $table);
        }
    }
}