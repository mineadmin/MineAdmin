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

namespace Mine\Kernel\JwtAuth\Event;

use App\Model\Permission\User;

final class UserLoginEvent
{
    public function __construct(
        private readonly User $user,
        private readonly string $ip,
        private readonly string $os,
        private readonly string $browser,
        private readonly bool $isLogin = true,
    ) {}

    public function getUser(): User
    {
        return $this->user;
    }

    public function isLogin(): bool
    {
        return $this->isLogin;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function getBrowser(): string
    {
        return $this->browser;
    }

    public function getOs(): string
    {
        return $this->os;
    }
}
