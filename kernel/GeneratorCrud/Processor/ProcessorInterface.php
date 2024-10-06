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

namespace Mine\GeneratorCrud\Processor;

use Mine\GeneratorCrud\Context;
use Mine\GeneratorCrud\Entity\GeneratorEntity;

interface ProcessorInterface
{
    public function process(Context $c): GeneratorEntity;
}
