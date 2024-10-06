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

namespace Mine\GeneratorCrud;

use Mine\GeneratorCrud\Entity\GeneratorEntity;
use Mine\GeneratorCrud\Entity\TableEntity;

interface GeneratorInterface
{
    /**
     * @return GeneratorEntity[]
     */
    public function generate(TableEntity $table, array $config = [], array $extra = []): array;
}
