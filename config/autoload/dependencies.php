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
use Casbin\Enforcer;
use Mine\Kernel\Casbin\Factory;
use Mine\Kernel\Upload\UploadInterface;

return [
    Enforcer::class => Factory::class,
    UploadInterface::class => Mine\Kernel\Upload\Factory::class,
];
