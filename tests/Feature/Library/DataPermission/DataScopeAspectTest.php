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

namespace HyperfTests\Feature\Library\DataPermission;

use App\Library\DataPermission\Aspects\DataScopeAspect;
use App\Library\DataPermission\Attribute\DataScope;
use App\Library\DataPermission\Context as DataPermissionContext;
use App\Library\DataPermission\Factory;
use App\Library\DataPermission\ScopeType;
use App\Model\Permission\User;
use Hyperf\Context\Context;
use Hyperf\Database\Query\Builder;
use Hyperf\Di\Aop\AnnotationMetadata;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 * @coversNothing
 */
final class DataScopeAspectTest extends TestCase
{
    /** @var Factory|MockObject */
    private $factory;

    /** @var DataScopeAspect */
    private $aspect;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = $this->createMock(Factory::class);
        $this->aspect = new DataScopeAspect($this->factory);
        Context::set(DataScopeAspect::CONTEXT_KEY, null);
        DataPermissionContext::setOnlyTables([]);
    }

    protected function tearDown(): void
    {
        Context::destroy(DataScopeAspect::CONTEXT_KEY);
        parent::tearDown();
    }

    public function testProcessWithAnnotationMetadata(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $annotation = new DataScope();
        $pjp->method('getAnnotationMetadata')->willReturn(new AnnotationMetadata(...[
            'class' => [DataScope::class => $annotation],
            'method' => [],
        ]));
        $pjp->expects(self::once())->method('process')->willReturn('result');

        $result = $this->aspect->process($pjp);
        self::assertEquals('result', $result);
    }

    public function testProcessWithBuilderRunSelect(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $pjp->className = Builder::class;
        $pjp->methodName = 'runSelect';

        $builder = $this->createMock(Builder::class);
        $builder->from = 'test_table';

        DataPermissionContext::setOnlyTables(['test_table']);

        $pjp->method('getInstance')->willReturn($builder);
        $pjp->method('getAnnotationMetadata')->willReturn(new AnnotationMetadata(...[
            'class' => [],
            'method' => [],
        ]));
        $pjp->expects(self::exactly(1))->method('process')
            ->willReturn('select_result');

        // 模拟 CurrentUser::ctxUser() 返回用户对象
        $user = new User();
        Context::set('current_user', $user);

        $this->factory->expects(self::once())->method('build')->with($builder, $user);
        Context::set(DataScopeAspect::CONTEXT_KEY, 1);
        $result = $this->aspect->process($pjp);
        self::assertEquals('select_result', $result);
    }

    public function testProcessWithBuilderDelete(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $pjp->className = Builder::class;
        $pjp->methodName = 'delete';
        $pjp->method('getAnnotationMetadata')->willReturn(new AnnotationMetadata(...[
            'class' => [],
            'method' => [],
        ]));
        $pjp->expects(self::once())->method('process')->willReturn('delete_result');

        $result = $this->aspect->process($pjp);
        self::assertEquals('delete_result', $result);
    }

    public function testProcessWithBuilderUpdate(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $pjp->className = Builder::class;
        $pjp->methodName = 'update';
        $pjp->method('getAnnotationMetadata')->willReturn(new AnnotationMetadata(...[
            'class' => [],
            'method' => [],
        ]));
        $pjp->expects(self::once())->method('process')->willReturn('update_result');

        $result = $this->aspect->process($pjp);
        self::assertEquals('update_result', $result);
    }

    public function testProcessWithoutAnnotationOrBuilder(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $pjp->className = 'OtherClass';
        $pjp->methodName = 'otherMethod';
        $pjp->method('getAnnotationMetadata')->willReturn(new AnnotationMetadata(...[
            'class' => [],
            'method' => [],
        ]));
        $pjp->expects(self::once())->method('process')->willReturn('other_result');

        $result = $this->aspect->process($pjp);
        self::assertEquals('other_result', $result);
    }

    public function testHandleDataScopeSetsContextAndCallsProcess(): void
    {
        $pjp = $this->createMock(ProceedingJoinPoint::class);
        $attribute = $this->createMock(DataScope::class);
        $attribute->method('getDeptColumn')->willReturn('dept_id');
        $attribute->method('getCreatedByColumn')->willReturn('created_by');
        $attribute->method('getScopeType')->willReturn(ScopeType::DEPT_CREATED_BY);
        $attribute->method('getOnlyTables')->willReturn(['table1']);
        $metaData = new AnnotationMetadata([DataScope::class => $attribute], []);
        $pjp->method('getAnnotationMetadata')->willReturn($metaData);
        $pjp->expects(self::once())->method('process')->willReturn('scoped_result');

        $result = $this->aspect->process($pjp);
        self::assertEquals('scoped_result', $result);
        self::assertNull(Context::get(DataScopeAspect::CONTEXT_KEY));
    }
}
