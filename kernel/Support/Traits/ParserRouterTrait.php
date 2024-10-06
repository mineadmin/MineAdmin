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

namespace Mine\Support\Traits;

trait ParserRouterTrait
{
    final protected function parse(mixed $callback): ?array
    {
        if (\is_array($callback) && \count($callback) === 2) {
            return $callback;
        }
        if (\is_string($callback)) {
            if (str_contains($callback, '@')) {
                $exp = explode('@', $callback);
            }
            if (str_contains($callback, '::')) {
                $exp = explode('::', $callback);
            }
            if (isset($exp) && \count($exp) === 2) {
                return $exp;
            }
        }
        return null;
    }
}
