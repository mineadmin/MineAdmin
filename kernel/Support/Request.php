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

namespace Mine\Support;

use Hyperf\HttpServer\Contract\RequestInterface;
use Mine\Support\Request\ClientIpRequestTrait;

class Request extends \Hyperf\HttpServer\Request implements RequestInterface
{
    use ClientIpRequestTrait;
}
