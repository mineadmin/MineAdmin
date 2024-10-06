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

namespace Mine\Jwt\Traits;

use Hyperf\Context\RequestContext;
use Lcobucci\JWT\UnencryptedToken;

trait RequestScopedTokenTrait
{
    public function getToken(): ?UnencryptedToken
    {
        return RequestContext::get()->getAttribute('token');
    }
}
