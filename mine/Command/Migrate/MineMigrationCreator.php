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

declare(strict_types = 1);
namespace Mine\Command\Migrate;

use Hyperf\Database\Migrations\MigrationCreator;

class MineMigrationCreator extends MigrationCreator
{

    public function stubPath(): string
    {
        return BASE_PATH . '/mine/Command/Migrate/Stubs';
    }
}
