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

namespace App\Kernel\Upload;

use App\Events\Attachment\UploadEvent;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Filesystem\FilesystemFactory;
use Hyperf\Stringable\Str;
use League\Flysystem\Filesystem;
use Psr\EventDispatcher\EventDispatcherInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Finder\SplFileInfo;

final class UploadManager
{
    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        private readonly FilesystemFactory $filesystemFactory,
        private readonly ConfigInterface $config
    ) {}

    public function upload(SplFileInfo $fileInfo, string $storageName)
    {
        $filesystem = $this->getFilesystem($storageName);
        $path = $this->generatorPath();
        $filename = $this->generatorId() . '.' . Str::lower($fileInfo->getExtension());
        $filesystem->write($path . '/' . $filename, file_get_contents($fileInfo->getRealPath()));
        $fileInfo = [
            'storage_mode' => $storageName,
            'object_name' => $filename,
            'mime_type' => mime_content_type($fileInfo->getRealPath()),
            'storage_path' => $path,
            'hash' => md5_file($fileInfo->getRealPath()),
            'suffix' => Str::lower($fileInfo->getExtension()),
            'size_byte' => $fileInfo->getSize(),
            'size_info' => \App\Kernel\Support\Filesystem::formatSize($fileInfo->getSize() * 1024),
            'url' => $filesystem->publicUrl($filename),
        ];
        $this->getDispatcher()->dispatch(new UploadEvent($fileInfo));
        return $fileInfo;
    }

    private function getDispatcher(): EventDispatcherInterface
    {
        return $this->dispatcher;
    }

    private function getFilesystemFactory(): FilesystemFactory
    {
        return $this->filesystemFactory;
    }

    private function getFilesystem(string $name): Filesystem
    {
        return $this->getFilesystemFactory()->get($name);
    }

    private function generatorPath(?string $filepath = null): string
    {
        if ($this->config->has('upload.path')) {
            return $this->config->get('upload.path') . '/' . $filepath;
        }
        return date('Y-m-d') . '/' . $filepath;
    }

    private function generatorId(): string
    {
        return Uuid::uuid4()->toString();
    }
}
