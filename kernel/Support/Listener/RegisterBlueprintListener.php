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

namespace Mine\Support\Listener;

use Hyperf\Database\Schema\Blueprint;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

#[Listener]
final class RegisterBlueprintListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            BootApplication::class,
        ];
    }

    public function process(object $event): void
    {
        Blueprint::macro('authorBy', function (string $created_by = 'created_by', string $updated_by = 'updated_by') {
            $this->bigInteger($created_by)->comment('创建者')->default(0);
            $this->bigInteger($updated_by)->comment('更新者')->default(0);
        });
    }
}
