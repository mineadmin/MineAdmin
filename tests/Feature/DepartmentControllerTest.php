<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createDepartmentTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function loginDepartmentTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantDepartmentPermission(User $user, string $permission): void
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

test('department crud keeps old endpoints and syncs users and leaders', function () {
    $user = createDepartmentTestUser();
    $member = createDepartmentTestUser();
    $leader = createDepartmentTestUser();
    $token = loginDepartmentTestUser($user)['access_token'];

    $this->withToken($token)
        ->postJson('/admin/department', ['name' => 'Tech'])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantDepartmentPermission($user, 'permission:department:save');
    grantDepartmentPermission($user, 'permission:department:index');
    grantDepartmentPermission($user, 'permission:department:update');
    grantDepartmentPermission($user, 'permission:department:delete');

    $this->withToken($token)
        ->postJson('/admin/department', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['name']]);

    $this->withToken($token)
        ->postJson('/admin/department', [
            'name' => 'Tech',
            'parent_id' => 0,
            'department_users' => [$member->id],
            'leader' => [$leader->id],
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $department = Department::query()->where('name', 'Tech')->first();

    expect($department)->not->toBeNull()
        ->and($department->departmentUsers()->whereKey($member->id)->exists())->toBeTrue()
        ->and($department->leader()->whereKey($leader->id)->exists())->toBeTrue();

    $this->withToken($token)
        ->getJson('/admin/department/list?name=Tech')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.list.0.name', 'Tech');

    $this->withToken($token)
        ->putJson("/admin/department/{$department->id}", ['name' => 'Product', 'department_users' => [], 'leader' => []])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $department->refresh();

    expect($department->name)->toBe('Product')
        ->and($department->departmentUsers()->count())->toBe(0)
        ->and($department->leader()->count())->toBe(0);

    $this->withToken($token)
        ->deleteJson('/admin/department', [$department->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Department::query()->whereKey($department->id)->exists())->toBeFalse();
});
