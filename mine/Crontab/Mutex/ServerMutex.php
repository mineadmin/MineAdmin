<?php

declare(strict_types=1);
namespace Mine\Crontab\Mutex;

use Mine\Crontab\MineCrontab;

interface ServerMutex
{
    /**
     * Attempt to obtain a server mutex for the given crontab.
     * @param MineCrontab $crontab
     * @return bool
     */
    public function attempt(MineCrontab $crontab): bool;

    /**
     * Get the server mutex for the given crontab.
     * @param MineCrontab $crontab
     * @return string
     */
    public function get(MineCrontab $crontab): string;
}
