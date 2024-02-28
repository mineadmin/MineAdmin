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
use Hyperf\DbConnection\Db;

use function Hyperf\Support\env;

beforeEach(function () {
    // 创建超级管理员
    Db::table('system_user')->insert([
        'id' => env('SUPER_ADMIN', 1),
        'username' => 'superAdmin',
        'password' => password_hash('admin123', PASSWORD_DEFAULT),
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
    // 创建管理员角色
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
    $this->prefix = '/system';
});
test('login', function () {
    testFailResponse($this->post($this->prefix . '/login', []));
    testFailResponse($this->post($this->prefix . '/login', [
        'username' => 'superAdmin',
    ]));
    $result = $this->post($this->prefix . '/login', [
        'username' => 'SuperAdmin',
        'password' => 'admin123',
    ]);
    testSuccessResponse($result);
    expect($result)->toHaveKey('data.token');
});
