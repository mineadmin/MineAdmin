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

namespace App\Http\Admin;

use App\Kernel\Auth\Support\RequestScopedTokenTrait;
use App\Model\Permission\User;
use App\Service\Permission\UserService;

class CurrentUser
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly UserService $service
    ) {}

    public function user(): ?User
    {
        return $this->service->getInfo($this->getToken());
    }

    public function refresh(): array
    {
        $token = $this->getToken();
        return $this->service->refreshToken($token);
    }
}
