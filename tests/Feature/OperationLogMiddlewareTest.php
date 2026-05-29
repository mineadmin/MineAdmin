<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use App\Models\UserOperationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createOperationLogMiddlewareUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function loginOperationLogMiddlewareUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantOperationLogMiddlewarePermission(User $user, string $permission): void
{
    $role = Role::query()->create([
        'name' => fake()->word(),
        'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'),
        'status' => Status::Normal,
        'sort' => 1,
        'remark' => '',
    ]);
    $menu = Menu::query()->create([
        'parent_id' => 0,
        'name' => $permission,
        'path' => '/'.fake()->unique()->word(),
        'component' => 'default',
        'redirect' => '',
        'status' => Status::Normal,
        'sort' => 1,
        'remark' => '',
    ]);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('successful admin endpoint writes operation log from endpoint attribute', function () {
    $user = createOperationLogMiddlewareUser();
    $token = loginOperationLogMiddlewareUser($user)['access_token'];
    grantOperationLogMiddlewarePermission($user, 'permission:menu:create');

    $this->withToken($token)
        ->postJson('/admin/menu', [
            'parent_id' => 0,
            'name' => 'system:operation-log',
            'path' => '/system/operation-log',
            'component' => 'modules/system/operation-log/index',
            'redirect' => '',
            'status' => Status::Normal->value,
            'sort' => 10,
            'remark' => 'operation log menu',
            'meta' => [
                'title' => '操作日志',
                'i18n' => 'menu.system.operationLog',
                'type' => 'M',
            ],
            'btnPermission' => [],
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $log = UserOperationLog::query()->first();

    expect($log)->not->toBeNull()
        ->and($log->username)->toBe($user->username)
        ->and($log->method)->toBe('POST')
        ->and($log->router)->toBe('/admin/menu')
        ->and($log->service_name)->toBe('创建菜单')
        ->and($log->ip)->toBe('127.0.0.1');
});

test('permission denied endpoint does not write operation log', function () {
    $user = createOperationLogMiddlewareUser();
    $token = loginOperationLogMiddlewareUser($user)['access_token'];

    $this->withToken($token)
        ->postJson('/admin/menu', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    expect(UserOperationLog::query()->count())->toBe(0);
});

test('operation log endpoints are not logged again', function () {
    $user = createOperationLogMiddlewareUser();
    UserOperationLog::query()->create([
        'username' => 'target-user',
        'method' => 'POST',
        'router' => '/admin/user',
        'service_name' => '创建用户',
        'ip' => '127.0.0.1',
    ]);
    $token = loginOperationLogMiddlewareUser($user)['access_token'];
    grantOperationLogMiddlewarePermission($user, 'log:userOperation:list');

    $this->withToken($token)
        ->getJson('/admin/user-operation-log/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(UserOperationLog::query()->count())->toBe(1);
});
