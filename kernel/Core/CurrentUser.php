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

namespace Mine\Kernel\Core;

use App\Model\Enums\User\Type;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use App\Service\PassportService;
use App\Service\Permission\UserService;
use Hyperf\Collection\Collection;
use Mine\Kernel\Jwt\Traits\RequestScopedTokenTrait;

final class CurrentUser
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly PassportService $service,
        private readonly UserService $userService
    ) {}

    public function user(): ?User
    {
        return $this->userService->getInfo($this->id());
    }

    public function refresh(): array
    {
        return $this->service->refreshToken($this->getToken());
    }

    public function id(): int
    {
        return (int) $this->getToken()->claims()->get('id');
    }

    public function menus(): Collection
    {
        return $this->user()->getMenus();
    }

    /**
     * @return Collection|Collection<int, Role>
     */
    public function roles(): Collection
    {
        return $this->user()->getRoles();
    }

    public function isSystem(): bool
    {
        return $this->user()->user_type === Type::SYSTEM;
    }

    public function isSuperAdmin(): bool
    {
        return $this->user()->isSuperAdmin();
    }
}
