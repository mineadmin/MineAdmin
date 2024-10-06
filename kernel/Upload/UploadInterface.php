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

use Mine\Upload\Exception\UploadFailException;

interface UploadInterface
{
    /**
     * @throws UploadFailException
     */
    public function upload(\SplFileInfo $fileInfo): Upload;
}
