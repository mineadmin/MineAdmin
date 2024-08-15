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

namespace App\Events\Attachment;

use Hyperf\Stringable\Str;

/**
 * 文件上传事件.
 */
final class UploadEvent
{
    /**
     * <code>
     * $fileInfo = [
     * 'storage_mode' => $storageName,
     * 'object_name' => $filename,
     * 'mime_type' => mime_content_type($fileInfo->getRealPath()),
     * 'storage_path' => $path,
     * 'hash' => md5_file($fileInfo->getRealPath()),
     * 'suffix' => Str::lower($fileInfo->getExtension()),
     * 'size_byte' => $fileInfo->getSize(),
     * 'size_info' => \App\Kernel\Support\Filesystem::formatSize($fileInfo->getSize() * 1024),
     * 'url' => $filesystem->publicUrl($filename),
     * ];
     * </code>.
     */
    public function __construct(
        private readonly array $fileInfo,
    ) {}

    public function getFileInfo(): array
    {
        return $this->fileInfo;
    }
}
