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

function createOperationLogTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['password' => '123456', 'user_type' => Type::System, 'status' => Status::Normal], $attributes));
}

function loginOperationLogTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', ['username' => $user->username, 'password' => '123456'])->json('data');
}

function grantOperationLogPermission(User $user, string $permission): void
{
    $role = Role::query()->create(['name' => fake()->word(), 'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'), 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);
    $menu = Menu::query()->create(['parent_id' => 0, 'name' => $permission, 'path' => '/'.fake()->unique()->word(), 'component' => 'default', 'redirect' => '', 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('user operation log list and delete endpoints work', function () {
    $user = createOperationLogTestUser();
    $log = UserOperationLog::query()->create(['username' => 'target-user', 'method' => 'POST', 'router' => '/admin/user', 'service_name' => '用户管理', 'ip' => '127.0.0.1']);
    UserOperationLog::query()->create(['username' => 'other-user', 'method' => 'PUT', 'router' => '/admin/role', 'service_name' => '角色管理', 'ip' => '127.0.0.1']);
    $token = loginOperationLogTestUser($user)['access_token'];

    $this->withToken($token)
        ->getJson('/admin/user-operation-log/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantOperationLogPermission($user, 'log:userOperation:list');
    grantOperationLogPermission($user, 'log:userOperation:delete');

    $this->withToken($token)
        ->getJson('/admin/user-operation-log/list?username=target&page=1&page_size=10')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.list.0.username', 'target-user');

    $this->withToken($token)
        ->deleteJson('/admin/user-operation-log', [$log->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(UserOperationLog::query()->whereKey($log->id)->exists())->toBeFalse();
});
