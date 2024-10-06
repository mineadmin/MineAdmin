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
use Mine\Casbin\Factory;
use Mine\Upload\UploadInterface;

return [
    Enforcer::class => Factory::class,
    UploadInterface::class => Mine\Upload\Factory::class,
];
