<?php

use App\Http\Common\ResultCode;
use App\Models\DataPermission\Policy;
use App\Models\Enums\DataPermission\PolicyType;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Menu;
use App\Models\Permission\Position;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPositionTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function loginPositionTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantPositionPermission(User $user, string $permission): void
{
    $role = Role::query()->create(['name' => fake()->word(), 'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'), 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);
    $menu = Menu::query()->create(['parent_id' => 0, 'name' => $permission, 'path' => '/'.fake()->unique()->word(), 'component' => 'default', 'redirect' => '', 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('position crud and data permission endpoints work', function () {
    $user = createPositionTestUser();
    $department = Department::query()->create(['name' => 'R&D']);
    $token = loginPositionTestUser($user)['access_token'];

    $this->withToken($token)
        ->postJson('/admin/position', ['name' => 'Engineer', 'dept_id' => $department->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    foreach (['permission:position:save', 'permission:position:index', 'permission:position:update', 'permission:position:delete', 'permission:position:data_permission'] as $permission) {
        grantPositionPermission($user, $permission);
    }

    $this->withToken($token)
        ->postJson('/admin/position', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['name', 'dept_id']]);

    $this->withToken($token)
        ->postJson('/admin/position', ['name' => 'Engineer', 'dept_id' => $department->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $position = Position::query()->where('name', 'Engineer')->first();

    expect($position)->not->toBeNull();

    $this->withToken($token)
        ->putJson("/admin/position/{$position->id}/data_permission", [
            'policy_type' => PolicyType::CustomDept->value,
            'value' => [$department->id],
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $policy = Policy::query()->where('position_id', $position->id)->first();

    expect($policy)->not->toBeNull()
        ->and($policy->policy_type)->toBe(PolicyType::CustomDept)
        ->and($policy->value)->toBe([$department->id]);

    $this->withToken($token)
        ->getJson('/admin/position/list?name=Engineer')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.list.0.name', 'Engineer');

    $this->withToken($token)
        ->putJson("/admin/position/{$position->id}", ['name' => 'Manager', 'dept_id' => $department->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect($position->refresh()->name)->toBe('Manager');

    $this->withToken($token)
        ->deleteJson('/admin/position', [$position->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Position::query()->whereKey($position->id)->exists())->toBeFalse();
});
