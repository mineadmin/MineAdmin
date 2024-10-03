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

namespace App\Service;

use Casbin\Enforcer;

final class PermissionService
{
    public function __construct(
        private readonly Enforcer $enforcer
    ) {}

    public function getEnforce(): Enforcer
    {
        return $this->enforcer;
    }
}
