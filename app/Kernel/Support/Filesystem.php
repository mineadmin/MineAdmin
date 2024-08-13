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

namespace App\Kernel\Support;

class Filesystem
{
    public static function copy(string $source, string $target, bool $deleteSource = true): void
    {
        $files = scandir($source);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            \Nette\Utils\FileSystem::copy($source . '/' . $file, $target . '/' . $file);
        }
        if ($deleteSource) {
            \Nette\Utils\FileSystem::delete($source);
        }
    }
}
