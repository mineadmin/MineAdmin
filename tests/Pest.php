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
use Hyperf\DbConnection\Db;
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
        Db::table('system_user')->truncate();
        $this->password = '123456';
        $this->username = 'admin';
        SystemUser::whereKey(env('SUPER_ADMIN', 1))->delete();
        $this->mock = SystemUser::create([
            'id' => env('SUPER_ADMIN', 1),
            'username' => $this->username,
            'password' => $this->password,
            'user_type' => '100',
            'nickname' => '创始人',
            'email' => 'admin@adminmine.com',
            'phone' => '16858888988',
            'signed' => '广阔天地，大有所为',
            'dashboard' => 'statistics',
            'created_by' => 0,
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        Db::table('system_role')->truncate();
        // Create Administrator Role
        Db::table('system_role')->insert([
            'id' => env('ADMIN_ROLE', 1),
            'name' => '超级管理员（创始人）',
            'code' => 'superAdmin',
            'data_scope' => 0,
            'sort' => 0,
            'created_by' => env('SUPER_ADMIN', 0),
            'updated_by' => 0,
            'status' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'remark' => '系统内置角色，不可删除',
        ]);
        if (env('DB_DRIVER') === 'pgsql') {
            Db::select("SELECT setval('system_user_id_seq', 1)");
            Db::select("SELECT setval('system_role_id_seq', 1)");
        }

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
