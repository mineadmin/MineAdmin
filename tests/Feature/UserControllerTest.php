<?php

use App\Http\Common\ResultCode;
use App\Models\DataPermission\Policy;
use App\Models\Enums\DataPermission\PolicyType;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createUserControllerUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function createUserControllerRole(array $attributes = []): Role
{
    return Role::query()->create(array_merge([
        'name' => fake()->word(),
        'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'),
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

function createUserControllerMenu(array $attributes = []): Menu
{
    return Menu::query()->create(array_merge([
        'parent_id' => 0,
        'name' => fake()->unique()->regexify('[A-Za-z0-9_:]{10}'),
        'path' => '/'.fake()->unique()->word(),
        'component' => 'default',
        'redirect' => '',
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

function createUserControllerDepartment(array $attributes = []): Department
{
    return Department::query()->create(array_merge([
        'name' => fake()->unique()->word(),
        'parent_id' => 0,
    ], $attributes));
}

function createUserControllerPolicy(User $user, PolicyType $policyType, ?array $value = null): Policy
{
    return $user->policy()->create([
        'policy_type' => $policyType,
        'value' => $value,
    ]);
}

function loginUserControllerUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantUserControllerPermission(User $user, string $permission): void
{
    $role = createUserControllerRole();
    $menu = createUserControllerMenu(['name' => $permission]);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

function userControllerPayload(array $attributes = []): array
{
    return array_merge([
        'username' => 'u'.fake()->unique()->numerify('########'),
        'user_type' => Type::System->value,
        'nickname' => fake()->firstName(),
        'phone' => fake()->numerify('1##########'),
        'email' => fake()->unique()->numerify('user########').'@qq.com',
        'avatar' => 'https://example.com/avatar.png',
        'signed' => 'hello',
        'status' => Status::Normal->value,
        'backend_setting' => ['theme' => 'dark'],
        'remark' => 'remark',
    ], $attributes);
}

test('user list requires permission and returns paginated users', function () {
    $user = createUserControllerUser();
    createUserControllerUser(['username' => 'target-user', 'nickname' => 'Target Nick']);
    $token = loginUserControllerUser($user)['access_token'];

    $this->getJson('/admin/user/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->getJson('/admin/user/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:index');

    $this->withToken($token)
        ->getJson('/admin/user/list?username=target&page=1&page_size=10&order_by=invalid&order_by_direction=sideways')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.list.0.username', 'target-user')
        ->assertJsonPath('data.list.0.nickname', 'Target Nick')
        ->assertJsonMissingPath('data.list.0.password');
});

test('user list applies self data permission scope', function () {
    $user = createUserControllerUser(['username' => 'scope-self-user']);
    createUserControllerUser(['username' => 'scope-hidden-user']);
    createUserControllerPolicy($user, PolicyType::Self);
    grantUserControllerPermission($user, 'permission:user:index');
    $token = loginUserControllerUser($user)['access_token'];

    $response = $this->withToken($token)
        ->getJson('/admin/user/list?page=1&page_size=10')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->json('data.list');

    expect(collect($response)->pluck('username')->all())->toBe(['scope-self-user']);
});

test('user list applies department tree data permission scope', function () {
    $parentDepartment = createUserControllerDepartment(['name' => 'scope-parent']);
    $childDepartment = createUserControllerDepartment([
        'name' => 'scope-child',
        'parent_id' => $parentDepartment->id,
    ]);
    $otherDepartment = createUserControllerDepartment(['name' => 'scope-other']);
    $user = createUserControllerUser(['username' => 'scope-tree-user']);
    $childUser = createUserControllerUser(['username' => 'scope-child-user']);
    $hiddenUser = createUserControllerUser(['username' => 'scope-other-user']);

    $user->department()->sync([$parentDepartment->id]);
    $childUser->department()->sync([$childDepartment->id]);
    $hiddenUser->department()->sync([$otherDepartment->id]);
    createUserControllerPolicy($user, PolicyType::DeptTree);
    grantUserControllerPermission($user, 'permission:user:index');
    $token = loginUserControllerUser($user)['access_token'];

    $response = $this->withToken($token)
        ->getJson('/admin/user/list?page=1&page_size=10&order_by=id&order_by_direction=asc')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 2)
        ->json('data.list');

    expect(collect($response)->pluck('username')->all())
        ->toBe(['scope-tree-user', 'scope-child-user']);
});

test('user create validates required fields and stores creator', function () {
    $user = createUserControllerUser();
    $token = loginUserControllerUser($user)['access_token'];
    $payload = userControllerPayload(['username' => 'created-user']);

    $this->postJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->postJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:save');

    $this->withToken($token)
        ->postJson('/admin/user', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['username', 'user_type', 'nickname']]);

    $this->withToken($token)
        ->postJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $createdUser = User::query()->where('username', 'created-user')->first();

    expect($createdUser)->not->toBeNull()
        ->and($createdUser->created_by)->toBe($user->id)
        ->and($createdUser->verifyPassword('123456'))->toBeTrue()
        ->and($createdUser->backend_setting)->toBe(['theme' => 'dark']);
});

test('user update endpoint changes target user fields and stores updater', function () {
    $user = createUserControllerUser();
    $targetUser = createUserControllerUser(['username' => 'old-user']);
    $token = loginUserControllerUser($user)['access_token'];
    $payload = userControllerPayload([
        'username' => 'updated-user',
        'nickname' => 'Updated',
        'status' => Status::Disable->value,
    ]);

    $this->withToken($token)
        ->putJson("/admin/user/{$targetUser->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:update');

    $this->withToken($token)
        ->putJson("/admin/user/{$targetUser->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $targetUser->refresh();

    expect($targetUser->username)->toBe('updated-user')
        ->and($targetUser->nickname)->toBe('Updated')
        ->and($targetUser->status)->toBe(Status::Disable)
        ->and($targetUser->updated_by)->toBe($user->id);
});

test('current user update keeps old path and permission behavior', function () {
    $user = createUserControllerUser(['username' => 'current-user']);
    $token = loginUserControllerUser($user)['access_token'];
    $payload = userControllerPayload([
        'username' => 'current-updated',
        'nickname' => 'CurrentUpdated',
    ]);

    $this->putJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->putJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:update');

    $this->withToken($token)
        ->putJson('/admin/user', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $user->refresh();

    expect($user->username)->toBe('current-updated')
        ->and($user->nickname)->toBe('CurrentUpdated')
        ->and($user->backend_setting)->toBe(['theme' => 'dark']);
});

test('user delete requires permission and removes role relations', function () {
    $user = createUserControllerUser();
    $targetUser = createUserControllerUser();
    $role = createUserControllerRole();
    $targetUser->roles()->sync([$role->id]);
    $token = loginUserControllerUser($user)['access_token'];

    $this->deleteJson('/admin/user')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->deleteJson('/admin/user', [$targetUser->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:delete');

    $this->withToken($token)
        ->deleteJson('/admin/user', ['ids' => [$targetUser->id]])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value);

    $this->withToken($token)
        ->deleteJson('/admin/user', [$targetUser->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(User::query()->whereKey($targetUser->id)->exists())->toBeFalse()
        ->and($role->users()->whereKey($targetUser->id)->exists())->toBeFalse();
});

test('user password reset matches old failure and success behavior', function () {
    $user = createUserControllerUser();
    $targetUser = createUserControllerUser(['password' => 'old-password']);
    $oldHash = $targetUser->password;
    $token = loginUserControllerUser($user)['access_token'];

    $this->putJson('/admin/user/password')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->putJson('/admin/user/password')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:password');

    $this->withToken($token)
        ->putJson('/admin/user/password')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Failure->value);

    $this->withToken($token)
        ->putJson('/admin/user/password', ['id' => $targetUser->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $targetUser->refresh();

    expect($targetUser->password)->not->toBe($oldHash)
        ->and($targetUser->verifyPassword('123456'))->toBeTrue();
});

test('user roles can be granted and read by role code', function () {
    $user = createUserControllerUser();
    $targetUser = createUserControllerUser();
    $roles = [
        createUserControllerRole(['name' => 'Admin', 'code' => 'admin_role']),
        createUserControllerRole(['name' => 'Operator', 'code' => 'operator_role']),
    ];
    $token = loginUserControllerUser($user)['access_token'];
    $uri = "/admin/user/{$targetUser->id}/roles";

    $this->withToken($token)
        ->putJson($uri, ['role_codes' => ['admin_role', 'operator_role']])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantUserControllerPermission($user, 'permission:user:setRole');
    grantUserControllerPermission($user, 'permission:user:getRole');

    $this->withToken($token)
        ->putJson($uri, ['role_codes' => ['admin_role', 'operator_role']])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $assignedRoles = $this->withToken($token)
        ->getJson($uri)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(2, 'data')
        ->json('data');

    expect(collect($assignedRoles)->pluck('code')->sort()->values()->all())
        ->toBe(['admin_role', 'operator_role'])
        ->and(collect($assignedRoles)->first())->toHaveKeys(['id', 'code', 'name'])
        ->and($targetUser->roles()->count())->toBe(count($roles));

    $this->withToken($token)
        ->putJson($uri, ['role_codes' => []])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect($targetUser->roles()->count())->toBe(0);
});
