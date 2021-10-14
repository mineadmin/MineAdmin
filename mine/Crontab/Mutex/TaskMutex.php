<?php

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
