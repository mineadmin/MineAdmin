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

namespace Mine\Swagger\Processor;

use OpenApi\Analysis;
use OpenApi\Processors\ProcessorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        $this->handle($analysis);
        return $analysis;
    }

    abstract public function handle(Analysis $analysis);
}
