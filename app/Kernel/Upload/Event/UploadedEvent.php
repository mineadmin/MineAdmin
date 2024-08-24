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

namespace App\Kernel\Upload\Event;

use App\Kernel\Upload\Upload;

final class UploadedEvent
{
    public function __construct(
        private readonly Upload $upload
    ) {}

    public function getUpload(): Upload
    {
        return $this->upload;
    }
}
