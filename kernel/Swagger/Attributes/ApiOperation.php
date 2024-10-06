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

namespace Mine\Swagger\Attributes;

use OpenApi\Annotations\Operation;
use OpenApi\Attributes\OperationTrait;

#[\Attribute(\Attribute::TARGET_METHOD)]
class ApiOperation extends Operation
{
    use OperationTrait;
}
