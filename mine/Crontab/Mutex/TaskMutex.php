<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Crontab\Mutex;

use Mine\Crontab\MineCrontab;

interface TaskMutex
{
    /**
     * Attempt to obtain a task mutex for the given crontab.
     * @param MineCrontab $crontab
     * @return bool
     */
    public function create(MineCrontab $crontab): bool;

    /**
     * Determine if a task mutex exists for the given crontab.
     * @param MineCrontab $crontab
     * @return bool
     */
    public function exists(MineCrontab $crontab): bool;

    /**
     * Clear the task mutex for the given crontab.
     * @param MineCrontab $crontab
     */
    public function remove(MineCrontab $crontab);
}
