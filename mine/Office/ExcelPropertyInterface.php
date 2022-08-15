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

namespace Mine\Office;

interface ExcelPropertyInterface
{
    public function import(\Mine\MineModel $model, ?\Closure $closure = null): bool;

    public function export(string $filename, array|\Closure $closure): \Psr\Http\Message\ResponseInterface;
}