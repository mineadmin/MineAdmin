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

namespace App\Library\DataPermission\Rule;

class CustomFuncFactory
{
    /**
     * @var array<string,\Closure>
     */
    private static array $customFunc = [];

    public static function registerCustomFunc(string $name, \Closure $func): void
    {
        self::$customFunc[$name] = $func;
    }

    public static function getCustomFunc(string $name): \Closure
    {
        if (isset(self::$customFunc[$name])) {
            return self::$customFunc[$name];
        }
        throw new \RuntimeException('Custom func not found');
    }
}
