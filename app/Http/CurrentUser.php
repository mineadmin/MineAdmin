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

namespace App\Http;

use App\Model\Enums\User\Status;
use App\Model\Permission\User;
use App\Service\PassportService;
use App\Service\Permission\MenuService;
use App\Service\Permission\UserService;
use Hyperf\Collection\Arr;
use Hyperf\Collection\Collection;
use Hyperf\Context\Context;
use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Traits\RequestScopedTokenTrait;

final class CurrentUser
{
    use RequestScopedTokenTrait;

    public function __construct(
        private readonly PassportService $service,
        private readonly UserService $userService,
        private readonly MenuService $menuService
    ) {}

    public static function ctxUser(): ?User
    {
        return Context::get('current_user');
    }

    public function user(): ?User
    {
        if (Context::has('current_user')) {
            return Context::get('current_user');
        }
        $user = $this->userService->getInfo($this->id());
        Context::set('current_user', $user);
        return $user;
    }

    public function refresh(): array
    {
        return $this->service->refreshToken($this->getToken());
    }

    public function id(): int
    {
        return (int) $this->getToken()->claims()->get(RegisteredClaims::ID);
    }

    public function isSuperAdmin(): bool
    {
        return $this->user()->isSuperAdmin();
    }

    public function filterCurrentUser(): array
    {
        $permissions = $this->user()
            ->getPermissions()
            ->pluck('name')
            ->unique();
        $menuList = $permissions->isEmpty()
            ? []
            : $this->menuService
                ->getList(['status' => Status::Normal, 'name' => $permissions->toArray()])
                ->toArray();
        $tree = [];
        $map = [];
        foreach ($menuList as &$menu) {
            $menu['children'] = [];
            $map[$menu['id']] = &$menu;
        }
        unset($menu);
        foreach ($menuList as &$menu) {
            $pid = $menu['parent_id'];
            if ($pid === 0 || ! isset($map[$pid])) {
                $tree[] = &$menu;
            } else {
                $map[$pid]['children'][] = &$menu;
            }
        }
        unset($menu);
        return $tree;
    }
}
