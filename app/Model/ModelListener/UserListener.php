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

namespace App\Model\ModelListener;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use App\Service\Permission\RoleService;
use App\Service\Permission\UserService;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Saving;
use Hyperf\ModelListener\Annotation\ModelListener;

#[ModelListener(
    models: [
        User::class,
        Role::class,
    ],
)]
class UserListener
{
    public function __construct(
        private readonly UserService $service,
        private readonly RoleService $roleService
    ) {}

    public function check(string $key): void
    {
        if ($this->service->count(['unique_username' => $key]) > 0) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, 'user.username_exist');
        }
        if ($this->roleService->count(['code' => $key]) > 0) {
            throw new BusinessException(ResultCode::UNPROCESSABLE_ENTITY, 'role.code_exist');
        }
    }

    public function creating(Creating $event)
    {
        $model = $event->getModel();
        if ($model instanceof User) {
            $this->check($model->username);
            if (! $model->isDirty('password')) {
                $model->resetPassword();
            }
        }
        if ($model instanceof Role) {
            $this->check($model->code);
        }
    }

    public function saving(Saving $event)
    {
        $model = $event->getModel();
        if ($model instanceof User && $model->isDirty('username')) {
            $this->check($model->username);
        }
        if ($model instanceof Role && $model->isDirty('code')) {
            $this->check($model->code);
        }
    }
}
