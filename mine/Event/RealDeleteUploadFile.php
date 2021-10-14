<?php

declare(strict_types=1);
namespace Mine\Event;

use App\System\Model\SystemUploadfile;
use League\Flysystem\Filesystem;

class RealDeleteUploadFile
{
    protected $model;

    protected $confirm = true;

    protected $filesystem;

    public function __construct(SystemUploadfile $model, Filesystem $filesystem)
    {
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    /**
     * 获取当前模型实例
     * @return SystemUploadfile
     */
    public function getModel(): SystemUploadfile
    {
        return $this->model;
    }

    /**
     * 获取文件处理系统
     * @return Filesystem
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 是否删除
     * @return bool
     */
    public function getConfirm(): bool
    {
        return $this->confirm;
    }

    public function setConfirm(bool $confirm): void
    {
        $this->confirm = $confirm;
    }
}