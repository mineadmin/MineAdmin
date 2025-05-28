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

namespace App\Library\DataPermission\Aspects;

use App\Http\CurrentUser;
use App\Library\DataPermission\Attribute\DataScope;
use App\Library\DataPermission\Context as DataPermissionContext;
use App\Library\DataPermission\Factory;
use Hyperf\Collection\Arr;
use Hyperf\Context\Context;
use Hyperf\Database\Query\Builder;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;

#[Aspect]
final class DataScopeAspect extends AbstractAspect
{
    public const CONTEXT_KEY = 'data_permission';

    public array $annotations = [
        DataScope::class,
    ];

    public array $classes = [
        Builder::class . '::update',
        Builder::class . '::delete',
        Builder::class . '::runSelect',
    ];

    public function __construct(
        private readonly Factory $factory
    ) {}

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if (
            isset($proceedingJoinPoint->getAnnotationMetadata()->class[DataScope::class])
            || isset($proceedingJoinPoint->getAnnotationMetadata()->method[DataScope::class])
        ) {
            return $this->handleDataScope($proceedingJoinPoint);
        }

        if ($proceedingJoinPoint->className === Builder::class) {
            if ($proceedingJoinPoint->methodName === 'runSelect') {
                return $this->handleSelect($proceedingJoinPoint);
            }
            if ($proceedingJoinPoint->methodName === 'delete') {
                return $this->handleDelete($proceedingJoinPoint);
            }
            if ($proceedingJoinPoint->methodName === 'update') {
                return $this->handleUpdate($proceedingJoinPoint);
            }
        }
        return $proceedingJoinPoint->process();
    }

    protected function handleDelete(ProceedingJoinPoint $proceedingJoinPoint)
    {
        return $proceedingJoinPoint->process();
    }

    protected function handleUpdate(ProceedingJoinPoint $proceedingJoinPoint)
    {
        return $proceedingJoinPoint->process();
    }

    protected function handleSelect(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /**
         * @var Builder $builder
         */
        $builder = $proceedingJoinPoint->getInstance();
        if (! \in_array($builder->from, DataPermissionContext::getOnlyTables() ?: [], true)) {
            return $proceedingJoinPoint->process();
        }
        if (Context::has(self::CONTEXT_KEY) && $user = CurrentUser::ctxUser()) {
            Context::destroy(self::CONTEXT_KEY);
            $this->factory->build(
                $builder,
                $user
            );
            Context::set(self::CONTEXT_KEY, 1);
        }
        return $proceedingJoinPoint->process();
    }

    protected function handleDataScope(ProceedingJoinPoint $proceedingJoinPoint)
    {
        Context::set(self::CONTEXT_KEY, 1);
        /**
         * @var null|DataScope $attribute
         */
        $attribute = Arr::get($proceedingJoinPoint->getAnnotationMetadata()->class, DataScope::class);
        if ($attribute === null) {
            $attribute = Arr::get($proceedingJoinPoint->getAnnotationMetadata()->method, DataScope::class);
        }
        if ($attribute === null) {
            return $proceedingJoinPoint->process();
        }
        DataPermissionContext::setDeptColumn($attribute->getDeptColumn());
        DataPermissionContext::setCreatedByColumn($attribute->getCreatedByColumn());
        DataPermissionContext::setScopeType($attribute->getScopeType());
        DataPermissionContext::setOnlyTables($attribute->getOnlyTables());
        $result = $proceedingJoinPoint->process();
        Context::destroy(self::CONTEXT_KEY);
        return $result;
    }
}
