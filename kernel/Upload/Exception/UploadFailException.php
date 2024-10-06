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

namespace Mine\Upload\Exception;

use Mine\Upload\Event\UploadEvent;

class UploadFailException extends \RuntimeException
{
    public function __construct(private readonly UploadEvent $event)
    {
        parent::__construct('Upload failed');
    }

    public function getEvent(): UploadEvent
    {
        return $this->event;
    }
}
