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

namespace Plugin\MineAdmin\CodeGenerator\Service;

use App\Service\IService;
use Hyperf\Database\Schema\Schema;

final class IndexService extends IService
{
    public function __construct() {}

    public function getTableListByCurrentDb(): array
    {
        return Schema::getAllTables();
    }
}
