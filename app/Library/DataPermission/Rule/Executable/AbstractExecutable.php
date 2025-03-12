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

namespace App\Library\DataPermission\Rule\Executable;

use App\Library\DataPermission\Rule\CustomFuncFactory;
use App\Model\DataPermission\Policy;
use App\Model\Permission\Department;
use App\Model\Permission\User;
use Hyperf\Collection\Collection;
use Hyperf\Context\ApplicationContext;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

abstract class AbstractExecutable
{
    public function __construct(
        private readonly User $user,
        private readonly Policy $policy
    ) {}

    abstract public function execute(): ?array;

    /**
     * @template T
     * @param class-string<T> $className
     * @return T
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getRepository(string $className): mixed
    {
        return ApplicationContext::getContainer()->get($className);
    }

    protected function getUser(): User
    {
        return $this->user;
    }

    protected function getPolicy(): Policy
    {
        return $this->policy;
    }

    /**
     * @return Collection<int,Department>
     */
    protected function loadCustomFunc(Policy $policy): Collection
    {
        $result = CustomFuncFactory::getCustomFunc($policy->value[0])->call($this, $this->getUser(), $this->getPolicy());
        if ($result instanceof Collection) {
            return $result;
        }
        throw new \RuntimeException('Custom func must return Collection');
    }
}
