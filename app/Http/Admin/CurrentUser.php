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
use App\Service\PassportService;
use App\Service\Permission\UserService;
use App\Service\PermissionService;
use Hyperf\Collection\Collection;

final class CurrentUser
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly PassportService $service,
        private readonly UserService $userService,
        private readonly PermissionService $permissionService
    ) {}

    public function user(): ?User
    {
        return $this->userService->getInfo($this->id());
    }

    public function refresh(): array
    {
        $token = $this->getToken();
        return $this->service->refreshToken($token);
    }

    public function id(): int
    {
        return (int) $this->getToken()->claims()->get('id');
    }

    public function menus(): Collection
    {
        return $this->permissionService->getMenuTreeByUserId($this->id());
    }

    public function roles(): Collection
    {
        return $this->permissionService->getRolesByUserId($this->id());
    }
}
