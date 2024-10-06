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

namespace Mine\Upload\Event;

use Mine\Upload\Upload;

final class UploadEvent
{
    private ?Upload $upload = null;

    public function __construct(
        private readonly \SplFileInfo $uploadFile,
    ) {}

    public function getUploadFile(): \SplFileInfo
    {
        return $this->uploadFile;
    }

    public function setUpload(?Upload $upload): void
    {
        $this->upload = $upload;
    }

    public function getUpload(): ?Upload
    {
        return $this->upload;
    }

    public function isUploaded(): bool
    {
        return $this->upload !== null;
    }
}
