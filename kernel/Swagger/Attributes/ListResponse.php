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

use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\Property;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class ListResponse extends ResultResponse
{
    protected function parserInstance(object|string $instance): array
    {
        $result[] = new Property(property: 'list', type: 'array', items: new Items(ref: $instance, description: '数据列表'));
        return $result;
    }
}
