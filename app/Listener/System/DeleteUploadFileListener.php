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

namespace App\Listener\System;

use App\Kernel\Event\RealDeleteAttachment;
use App\Model\DataCenter\Attachment;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use League\Flysystem\FilesystemException;

/**
 * Class DeleteUploadFileListener.
 */
#[Listener]
class DeleteUploadFileListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            RealDeleteAttachment::class,
        ];
    }

    /**
     * @throws FilesystemException
     */
    public function process(object $event): void
    {
        $filePath = $this->getAttachmentPath($event->getModel());
        try {
            $event->getFilesystem()->delete($filePath);
        } catch (\Exception $e) {
            // 文件删除失败，跳过删除数据
            $event->setConfirm(false);
        }
    }

    /**
     * 获取文件路径.
     */
    public function getAttachmentPath(Attachment $model): string
    {
        return $model->storage_path . '/' . $model->object_name;
    }
}
