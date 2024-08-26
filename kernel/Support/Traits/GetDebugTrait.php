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

namespace Mine\Kernel\Support\Traits;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;

trait GetDebugTrait
{
    public function isDebug(): bool
    {
        return (bool) ApplicationContext::getContainer()->get(ConfigInterface::class)->get('debug');
    }
}
