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

namespace App\Library\DataPermission;

enum ScopeType: int
{
    // 只根据部门过滤
    case DEPT = 1;

    // 只根据创建人过滤
    case CREATED_BY = 2;

    // 根据部门 and 创建人过滤
    case DEPT_CREATED_BY = 3;

    // 根据部门 or 创建人过滤
    case DEPT_OR_CREATED_BY = 4;
}
