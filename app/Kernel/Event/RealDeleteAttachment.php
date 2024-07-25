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

namespace App\Kernel\Event;

use Hyperf\DbConnection\Model\Model;
use League\Flysystem\Filesystem;

class RealDeleteAttachment
{
    protected Model $model;

    protected bool $confirm = true;

    protected Filesystem $filesystem;

    public function __construct(Model $model, Filesystem $filesystem)
    {
        $this->model = $model;
        $this->filesystem = $filesystem;
    }

    /**
     * 获取当前模型实例.
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * 获取文件处理系统
     */
    public function getFilesystem(): Filesystem
    {
        return $this->filesystem;
    }

    /**
     * 是否删除.
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
