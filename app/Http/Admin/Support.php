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
use Hyperf\Context\ApplicationContext;

function user(): ?User
{
    return ApplicationContext::getContainer()->get(CurrentUser::class)->user();
}
