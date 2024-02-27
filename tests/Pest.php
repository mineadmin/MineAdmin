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
use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use HyperfTests\HttpTestCase;
use Mine\Aspect\AuthAspect;
use Mine\Aspect\OperationLogAspect;
use Mine\Aspect\PermissionAspect;
use Mine\Helper\LoginUser;
use Mine\Interfaces\ServiceInterface\UserServiceInterface;
use Mine\MineRequest;

function testSuccessResponse(mixed $result)
{
    expect($result)
        ->toBeArray()
        ->toHaveKey('requestId')
        ->toHaveKey('success')
        ->toHaveKey('message')
        ->toHaveKey('code')
        ->toHaveKey('data')
        ->and($result['code'])
        ->toEqual(200);
}

function testFailResponse(mixed $result)
{
    expect($result)
        ->toBeArray()
        ->toHaveKey('requestId')
        ->toHaveKey('success')
        ->toHaveKey('message')
        ->toHaveKey('code')
        ->and($result['success'])
        ->toBeFalse()
        ->and($result['code'] !== 200)
        ->toBeTrue();
}

uses(HttpTestCase::class)
    ->beforeEach(function () {
        $aspect = new class() extends AuthAspect {
            public function process(ProceedingJoinPoint $proceedingJoinPoint)
            {
                return $proceedingJoinPoint->process();
            }
        };
        ApplicationContext::getContainer()
            ->set(AuthAspect::class, $aspect);
        $userServiceInterface = Mockery::mock(UserServiceInterface::class);
        $mineRequest = Mockery::mock(MineRequest::class);
        $loginUser = Mockery::mock(LoginUser::class);
        $permissionAspect = new class($userServiceInterface, $mineRequest, $loginUser) extends PermissionAspect {
            public function process(ProceedingJoinPoint $proceedingJoinPoint)
            {
                return $proceedingJoinPoint->process();
            }
        };
        ApplicationContext::getContainer()->set(PermissionAspect::class, $permissionAspect);

        $operationLogAspect = new class() extends OperationLogAspect {
            public function process(ProceedingJoinPoint $proceedingJoinPoint)
            {
                return $proceedingJoinPoint->process();
            }
        };
        ApplicationContext::getContainer()->set(OperationLogAspect::class, $operationLogAspect);

        $config = ApplicationContext::getContainer()->get(ConfigInterface::class);
        $config->set('mineadmin.data_scope_enabled', false);
    })
    ->group('http testing')
    ->in('HttpCases');
