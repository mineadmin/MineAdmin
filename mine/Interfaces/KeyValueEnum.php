<?php

declare(strict_types=1);

/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

namespace Mine\Interfaces;

/**
 * key/value 枚举接口
 */
interface KeyValueEnum
{
    public function key();

    public function value();
}