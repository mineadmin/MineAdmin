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

namespace App\Listener\Upload;

use App\Kernel\Upload\Listener\UploadListener as AbstractUploadListener;
use Hyperf\Event\Annotation\Listener;

#[Listener]
class UploadListener extends AbstractUploadListener
{
    public const ADAPTER_NAME = 'local';
}
