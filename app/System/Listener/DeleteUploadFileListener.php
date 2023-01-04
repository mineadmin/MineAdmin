<?php

declare(strict_types=1);
namespace App\System\Listener;

use App\System\Model\SystemUploadfile;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use League\Flysystem\FileNotFoundException;
use Mine\Event\RealDeleteUploadFile;

/**
 * Class DeleteUploadFileListener
 * @package App\System\Listener
 */
#[Listener]
class DeleteUploadFileListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            RealDeleteUploadFile::class
        ];
    }

    /**
     * @param RealDeleteUploadFile $event
     */
    public function process(object $event): void
    {
        $filePath = $this->getFilePath($event->getModel());
        try {
            $event->getFilesystem()->delete($filePath);
        } catch (FileNotFoundException $e) {
            // 文件删除失败，跳过删除数据
            $event->setConfirm(false);
        }
    }

    /**
     * 获取文件路径
     * @param SystemUploadfile $model
     * @return string
     */
    public function getFilePath(SystemUploadfile $model): string
    {
        return $model->storage_path.'/'.$model->object_name;
    }

}