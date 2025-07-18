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

namespace App\Library\DataPermission\Scope;

use Hyperf\DbConnection\Model\Model;

/**
 * @internal
 * @mixin Model
 */
trait DataScopes
{
    public static function bootDataScopes(): void
    {
        static::addGlobalScope(new DataScope());
    }
}
