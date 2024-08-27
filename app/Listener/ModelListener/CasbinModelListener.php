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

namespace App\Listener\ModelListener;

use App\Model\Permission\Menu;
use App\Model\Permission\Role;
use App\Model\Permission\User;
use Casbin\Enforcer;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Logger\LoggerFactory;
use Hyperf\ModelListener\Annotation\ModelListener;
use Mine\Kernel\Support\Traits\GetDebugTrait;
use Psr\Log\LoggerInterface;

#[ModelListener(models: [
    Menu::class,
    Role::class,
    User::class,
])]
final class CasbinModelListener
{
    use GetDebugTrait;

    private LoggerInterface $logger;

    public function __construct(
        private readonly Enforcer $enforcer,
        private readonly StdoutLoggerInterface $stdoutLogger,
        LoggerFactory $loggerFactory
    ) {
        $this->logger = $loggerFactory->get();
    }

    public function creating(Creating $creating)
    {
        $model = $creating->getModel();
        switch ($model) {
            case $model instanceof Menu:
                if (! $this->enforcer->hasPermissionForUser('SuperAdmin', $model->code)) {
                    $this->enforcer->addPermissionForUser('SuperAdmin', $model->code);
                    $message = 'Saving Menu: ' . $model->code;
                }
                break;
            case $model instanceof Role:
                $this->enforcer->addRoleForUser('SuperAdmin', $model->code);
                $message = 'Saving Role: ' . $model->code;
                break;
        }
        if (isset($message)) {
            $this->isDebug() ? $this->stdoutLogger->debug($message) : $this->logger->debug($message);
        }
    }

    public function deleted(Deleted $deleted)
    {
        $model = $deleted->getModel();
        switch ($model) {
            case $model instanceof Menu:
                $this->enforcer->deletePermission($model->code);
                $message = 'Deleted Menu: ' . $model->code;
                break;
            case $model instanceof Role:
                $this->enforcer->deleteRole($model->code);
                $message = 'Deleted Role: ' . $model->code;
                break;
            case $model instanceof User:
                $this->enforcer->deleteUser($model->username);
                $message = 'Deleted User: ' . $model->username;
                break;
        }
        if (isset($message)) {
            $this->isDebug() ? $this->stdoutLogger->debug($message) : $this->logger->debug($message);
        }
    }
}
