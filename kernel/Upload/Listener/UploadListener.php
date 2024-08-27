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

namespace Mine\Kernel\Upload\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Stringable\Str;
use League\Flysystem\Filesystem;
use Mine\Kernel\Upload\Event\UploadEvent;
use Mine\Kernel\Upload\Upload;
use Ramsey\Uuid\Uuid;

abstract class UploadListener implements ListenerInterface
{
    public const ADAPTER_NAME = 'local';

    private Filesystem $filesystem;

    public function __construct(
        FilesystemFactory $filesystemFactory
    ) {
        $this->filesystem = $filesystemFactory->get(static::ADAPTER_NAME);
    }

    public function listen(): array
    {
        return [
            UploadEvent::class,
        ];
    }

    public function process(object $event): void
    {
        if ($event instanceof UploadEvent) {
            $fileInfo = $event->getUploadFile();
            $path = $this->generatorPath();
            $filename = $this->generatorId() . '.' . Str::lower($fileInfo->getExtension());
            $this->filesystem->write($path . '/' . $filename, file_get_contents($fileInfo->getRealPath()));
            $event->setUpload(new Upload(
                static::ADAPTER_NAME,
                $filename,
                mime_content_type($fileInfo->getRealPath()),
                $path,
                md5_file($fileInfo->getRealPath()),
                Str::lower($fileInfo->getExtension()),
                $fileInfo->getSize(),
                $this->formatSize($fileInfo->getSize() * 1024),
                $this->filesystem->publicUrl($path . '/' . $filename)
            ));
        }
    }

    protected function generatorPath(): string
    {
        return date('Y-m-d');
    }

    protected function generatorId(): string
    {
        return Uuid::uuid4()->toString();
    }

    protected function formatSize(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; ++$i) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}
