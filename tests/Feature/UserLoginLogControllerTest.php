<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createLoginLogTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['password' => '123456', 'user_type' => Type::System, 'status' => Status::Normal], $attributes));
}

function loginLoginLogTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', ['username' => $user->username, 'password' => '123456'])->json('data');
}

function grantLoginLogPermission(User $user, string $permission): void
{
    $role = Role::query()->create(['name' => fake()->word(), 'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'), 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);
    $menu = Menu::query()->create(['parent_id' => 0, 'name' => $permission, 'path' => '/'.fake()->unique()->word(), 'component' => 'default', 'redirect' => '', 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('user login log list and delete endpoints work', function () {
    $user = createLoginLogTestUser();
    $log = UserLoginLog::query()->create(['username' => 'target-user', 'ip' => '127.0.0.1', 'os' => 'MAC', 'browser' => 'Chrome', 'status' => 1]);
    UserLoginLog::query()->create(['username' => 'other-user', 'ip' => '127.0.0.1', 'os' => 'MAC', 'browser' => 'Chrome', 'status' => 2]);
    $token = loginLoginLogTestUser($user)['access_token'];

    $this->withToken($token)
        ->getJson('/admin/user-login-log/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantLoginLogPermission($user, 'log:userLogin:list');
    grantLoginLogPermission($user, 'log:userLogin:delete');

    $this->withToken($token)
        ->getJson('/admin/user-login-log/list?username=target&page=1&page_size=10')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.list.0.username', 'target-user');

    $this->withToken($token)
        ->deleteJson('/admin/user-login-log', [$log->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(UserLoginLog::query()->whereKey($log->id)->exists())->toBeFalse();
});

test('user login log list supports migrated search fields', function () {
    $user = createLoginLogTestUser();
    grantLoginLogPermission($user, 'log:userLogin:list');
    $token = loginLoginLogTestUser($user)['access_token'];

    UserLoginLog::query()->create([
        'username' => 'filtered-user',
        'ip' => '10.10.10.10',
        'os' => 'Windows',
        'browser' => 'Edge',
        'status' => 2,
        'message' => 'password error',
        'remark' => 'manual review',
        'login_time' => '2026-05-10 12:00:00',
    ]);

    UserLoginLog::query()->create([
        'username' => 'other-filter-user',
        'ip' => '10.10.10.11',
        'os' => 'MacOS',
        'browser' => 'Chrome',
        'status' => 1,
        'message' => 'login success',
        'remark' => 'normal',
        'login_time' => '2026-05-11 12:00:00',
    ]);

    $filters = [
        'ip' => '10.10.10.10',
        'os' => 'Windows',
        'browser' => 'Edge',
        'status' => 2,
        'message' => 'password error',
        'remark' => 'manual review',
        'login_time' => ['2026-05-10 00:00:00', '2026-05-10 23:59:59'],
    ];

    foreach ($filters as $field => $value) {
        $this->withToken($token)
            ->getJson('/admin/user-login-log/list?'.http_build_query([$field => $value, 'page' => 1, 'page_size' => 10]))
            ->assertSuccessful()
            ->assertJsonPath('code', ResultCode::Success->value)
            ->assertJsonPath('data.total', 1)
            ->assertJsonPath('data.list.0.username', 'filtered-user');
    }
});
