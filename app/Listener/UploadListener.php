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

namespace App\Listener;

use App\Events\Attachment\UploadEvent;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
final class UploadListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UploadEvent::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadEvent) {
        }
    }
}
