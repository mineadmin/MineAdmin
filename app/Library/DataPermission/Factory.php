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

namespace App\Library\DataPermission;

use App\Library\DataPermission\Rule\Rule;
use App\Model\DataPermission\Policy;
use App\Model\Enums\DataPermission\PolicyType;
use App\Model\Permission\User;
use Hyperf\Context\ApplicationContext;
use Hyperf\Database\Query\Builder;

class Factory
{
    public function __construct(
        private readonly Rule $rule
    ) {}

    public static function make(): self
    {
        return ApplicationContext::getContainer()->get(self::class);
    }

    public function build(
        Builder $builder,
        User $user,
    ): void {
        if ($user->isSuperAdmin()) {
            return;
        }
        if (($policy = $user->getPolicy()) === null) {
            return;
        }
        $scopeType = Context::getScopeType();
        if ($policy->policy_type === PolicyType::CustomFunc) {
            $customFunc = $policy->value[0] ?? null;
            if (! \is_string($customFunc)) {
                throw new \RuntimeException(\sprintf('Invalid custom function: %s', $customFunc));
            }
            $this->rule->loadCustomFunc($customFunc, $builder, $user, $policy, $scopeType);
        }
        switch ($scopeType) {
            case ScopeType::CREATED_BY:
                self::handleCreatedBy($user, $policy, $builder);
                break;
            case ScopeType::DEPT:
                self::handleDept($user, $policy, $builder);
                break;
            case ScopeType::DEPT_CREATED_BY:
                self::handleDeptCreatedBy($user, $policy, $builder);
                break;
            case ScopeType::DEPT_OR_CREATED_BY:
                self::handleDeptOrCreatedBy($user, $policy, $builder);
                break;
        }
    }

    private function handleCreatedBy(User $user, Policy $policy, Builder $builder): void
    {
        $builder->when($this->rule->getCreatedByList($user, $policy), static function (Builder $query, array $createdByList) {
            $query->whereIn(Context::getCreatedByColumn(), $createdByList);
        });
    }

    private function handleDept(User $user, Policy $policy, Builder $builder): void
    {
        $builder->when($this->rule->getDeptIds($user, $policy), static function (Builder $query, array $deptList) {
            $query->whereIn(Context::getDeptColumn(), $deptList);
        });
    }

    private function handleDeptCreatedBy(User $user, Policy $policy, Builder $builder): void
    {
        $builder->when($this->rule->getDeptIds($user, $policy), static function (Builder $query, array $deptList) {
            $query->whereIn(Context::getDeptColumn(), $deptList);
        })->when($this->rule->getCreatedByList($user, $policy), static function (Builder $query, array $createdByList) {
            $query->whereIn(Context::getCreatedByColumn(), $createdByList);
        });
    }

    private function handleDeptOrCreatedBy(User $user, Policy $policy, Builder $builder): void
    {
        $createdByList = $this->rule->getCreatedByList($user, $policy);
        $deptList = $this->rule->getDeptIds($user, $policy);
        $builder->where(static function (Builder $query) use ($createdByList, $deptList) {
            if ($createdByList) {
                $query->whereIn(Context::getCreatedByColumn(), $createdByList);
            }
            if ($deptList) {
                $query->orWhereIn(Context::getDeptColumn(), $deptList);
            }
        });
    }
}
