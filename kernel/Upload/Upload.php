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

final class Upload
{
    public function __construct(
        private string $storageMode,
        private string $objectName,
        private string $mimeType,
        private string $storagePath,
        private string $hash,
        private string $suffix,
        private int $sizeByte,
        private int $sizeInfo,
        private string $url
    ) {}

    public function getStorageMode(): string
    {
        return $this->storageMode;
    }

    public function setStorageMode(string $storageMode): void
    {
        $this->storageMode = $storageMode;
    }

    public function getObjectName(): string
    {
        return $this->objectName;
    }

    public function setObjectName(string $objectName): void
    {
        $this->objectName = $objectName;
    }

    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): void
    {
        $this->mimeType = $mimeType;
    }

    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    public function setStoragePath(string $storagePath): void
    {
        $this->storagePath = $storagePath;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function setHash(string $hash): void
    {
        $this->hash = $hash;
    }

    public function getSuffix(): string
    {
        return $this->suffix;
    }

    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
    }

    public function getSizeByte(): int
    {
        return $this->sizeByte;
    }

    public function setSizeByte(int $sizeByte): void
    {
        $this->sizeByte = $sizeByte;
    }

    public function getSizeInfo(): int
    {
        return $this->sizeInfo;
    }

    public function setSizeInfo(int $sizeInfo): void
    {
        $this->sizeInfo = $sizeInfo;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
