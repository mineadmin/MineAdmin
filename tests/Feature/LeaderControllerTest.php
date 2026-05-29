<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Leader;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createLeaderTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['password' => '123456', 'user_type' => Type::System, 'status' => Status::Normal], $attributes));
}

function loginLeaderTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', ['username' => $user->username, 'password' => '123456'])->json('data');
}

function grantLeaderPermission(User $user, string $permission): void
{
    $role = Role::query()->create(['name' => fake()->word(), 'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'), 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);
    $menu = Menu::query()->create(['parent_id' => 0, 'name' => $permission, 'path' => '/'.fake()->unique()->word(), 'component' => 'default', 'redirect' => '', 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('leader endpoints create list and delete by department and users', function () {
    $user = createLeaderTestUser();
    $leader = createLeaderTestUser();
    $department = Department::query()->create(['name' => 'Tech']);
    $token = loginLeaderTestUser($user)['access_token'];

    $this->withToken($token)
        ->postJson('/admin/leader', ['dept_id' => $department->id, 'user_id' => [$leader->id]])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    foreach (['permission:leader:save', 'permission:leader:index', 'permission:leader:delete'] as $permission) {
        grantLeaderPermission($user, $permission);
    }

    $this->withToken($token)
        ->postJson('/admin/leader', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['user_id', 'dept_id']]);

    $this->withToken($token)
        ->postJson('/admin/leader', ['dept_id' => $department->id, 'user_id' => [$leader->id]])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Leader::query()->where('dept_id', $department->id)->where('user_id', $leader->id)->exists())->toBeTrue();

    $this->withToken($token)
        ->getJson("/admin/leader/list?dept_id={$department->id}")
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.list.0.dept_id', $department->id)
        ->assertJsonPath('data.list.0.user_id', $leader->id);

    $this->withToken($token)
        ->deleteJson('/admin/leader', ['dept_id' => $department->id, 'user_ids' => [$leader->id]])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Leader::query()->where('dept_id', $department->id)->where('user_id', $leader->id)->exists())->toBeFalse();
});
