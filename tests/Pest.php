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
use App\System\Model\SystemUser;
use Hyperf\Context\ApplicationContext;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use HyperfTests\HttpTestCase;
use HyperfTests\MineControllerTestCase;
use Mine\Aspect\OperationLogAspect;

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

uses(HttpTestCase::class, MineControllerTestCase::class)
    ->beforeEach(function () {
        // Create Super Administrator
        $this->password = '12345678';
        $this->username = 'superAdmin';
        $this->mock = SystemUser::whereKey(env('SUPER_ADMIN', 1))->first();
        $operationLogAspect = new class() extends OperationLogAspect {
            public function process(ProceedingJoinPoint $proceedingJoinPoint)
            {
                return $proceedingJoinPoint->process();
            }
        };
        ApplicationContext::getContainer()->set(OperationLogAspect::class, $operationLogAspect);
    })
    ->group('module http testing')
    ->in('Feature');
