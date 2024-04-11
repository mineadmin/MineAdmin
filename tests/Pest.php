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
use Hyperf\Stringable\Str;
use HyperfTests\HttpTestCase;
use HyperfTests\MineControllerTestCase;
use Mine\Aspect\OperationLogAspect;

uses(HttpTestCase::class, MineControllerTestCase::class)
    ->beforeEach(function () {
        // Create Super Administrator
        $this->password = Str::random(10);
        $this->username = Str::random(10);
        $this->mock = SystemUser::create([
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
        putenv(sprintf('SUPER_ADMIN=%d', $this->mock->id));
        // Create Administrator Role
        $roleId = Db::table('system_role')->insertGetId([
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
        putenv(sprintf('ADMIN_ROLE=%d', $roleId));
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
