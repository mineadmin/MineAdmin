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

namespace App\Http\Admin\Vo\Permission\Menu;

use App\Schema\MenuSchema;
use Hyperf\Swagger\Annotation\Items;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(
    title: '菜单列表',
    description: '菜单列表',
    items: new Items(ref: MenuSchema::class)
)]
class ListVo {}
