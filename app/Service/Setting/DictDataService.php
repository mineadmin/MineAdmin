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

namespace App\Service\Setting;

use App\Repository\Setting\DictDataRepository;
use App\Service\IService;

final class DictDataService extends IService
{
    public function __construct(
        protected readonly DictDataRepository $repository,
    ) {}
}
