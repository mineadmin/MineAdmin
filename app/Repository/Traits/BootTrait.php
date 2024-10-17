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

namespace App\Repository\Traits;

trait BootTrait
{
    protected function startBoot(...$params): void
    {
        $traits = class_uses_recursive(static::class);
        foreach ($traits as $trait) {
            $method = 'boot' . class_basename($trait);
            if (method_exists($this, $method)) {
                $this->{$method}(...$params);
            }
        }
    }
}
