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

namespace Mine\Core\Subscriber\Upload;

use Mine\Upload\Listener\UploadListener as AbstractUploadListener;

final class UploadSubscriber extends AbstractUploadListener
{
    public const ADAPTER_NAME = 'local';
}
