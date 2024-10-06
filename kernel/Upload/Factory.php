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

namespace Mine\Upload;

use Mine\Upload\Event\UploadEvent;
use Mine\Upload\Exception\UploadFailException;
use Psr\EventDispatcher\EventDispatcherInterface;

final class Factory implements UploadInterface
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function upload(\SplFileInfo $fileInfo): Upload
    {
        $event = new UploadEvent($fileInfo);
        $this->dispatcher->dispatch($event);
        if ($event->isUploaded()) {
            return $event->getUpload();
        }
        throw new UploadFailException($event);
    }
}
