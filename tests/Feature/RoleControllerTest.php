<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createRoleControllerUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function createRoleControllerRole(array $attributes = []): Role
{
    return Role::query()->create(array_merge([
        'name' => fake()->word(),
        'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'),
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

function createRoleControllerMenu(array $attributes = []): Menu
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

function loginRoleControllerUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantRoleControllerPermission(User $user, string $permission): void
{
    $role = createRoleControllerRole();
    $menu = createRoleControllerMenu(['name' => $permission]);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('role list requires permission and returns paginated roles', function () {
    $user = createRoleControllerUser();
    createRoleControllerRole(['name' => 'Admin', 'code' => 'admin']);
    $token = loginRoleControllerUser($user)['access_token'];

    $this->getJson('/admin/role/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->getJson('/admin/role/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantRoleControllerPermission($user, 'permission:role:index');

    $this->withToken($token)
        ->getJson('/admin/role/list?name=Adm&page=1&page_size=10')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.list.0.name', 'Admin')
        ->assertJsonPath('data.list.0.code', 'admin');
});

test('role create validates and stores creator', function () {
    $user = createRoleControllerUser();
    $token = loginRoleControllerUser($user)['access_token'];
    $payload = [
        'name' => 'Operator',
        'code' => 'operator',
        'sort' => 10,
        'status' => Status::Normal->value,
        'remark' => 'ops',
    ];

    $this->withToken($token)
        ->postJson('/admin/role', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantRoleControllerPermission($user, 'permission:role:save');

    $this->withToken($token)
        ->postJson('/admin/role', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['name', 'code', 'sort']]);

    $this->withToken($token)
        ->postJson('/admin/role', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $role = Role::query()->where('code', 'operator')->first();

    expect($role)->not->toBeNull()
        ->and($role->name)->toBe('Operator')
        ->and($role->created_by)->toBe($user->id)
        ->and($role->remark)->toBe('ops');
});

test('role update validates unique code and stores updater', function () {
    $user = createRoleControllerUser();
    $role = createRoleControllerRole(['name' => 'Old', 'code' => 'old_role']);
    createRoleControllerRole(['code' => 'used_code']);
    $token = loginRoleControllerUser($user)['access_token'];
    $payload = [
        'name' => 'New',
        'code' => 'new_role',
        'sort' => 20,
        'status' => Status::Disable->value,
        'remark' => 'updated',
    ];

    $this->withToken($token)
        ->putJson("/admin/role/{$role->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantRoleControllerPermission($user, 'permission:role:update');

    $this->withToken($token)
        ->putJson("/admin/role/{$role->id}", array_merge($payload, ['code' => 'used_code']))
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value);

    $this->withToken($token)
        ->putJson("/admin/role/{$role->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $role->refresh();

    expect($role->name)->toBe('New')
        ->and($role->code)->toBe('new_role')
        ->and($role->status)->toBe(Status::Disable)
        ->and($role->updated_by)->toBe($user->id);
});

test('role delete requires permission and detaches relations', function () {
    $user = createRoleControllerUser();
    $role = createRoleControllerRole();
    $menu = createRoleControllerMenu();
    $role->users()->sync([$user->id]);
    $role->menus()->sync([$menu->id]);
    $token = loginRoleControllerUser($user)['access_token'];

    $this->deleteJson('/admin/role')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->deleteJson('/admin/role', [$role->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantRoleControllerPermission($user, 'permission:role:delete');

    $this->withToken($token)
        ->deleteJson('/admin/role', [$role->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Role::query()->whereKey($role->id)->exists())->toBeFalse()
        ->and($user->roles()->whereKey($role->id)->exists())->toBeFalse()
        ->and($menu->roles()->whereKey($role->id)->exists())->toBeFalse();
});

test('role permissions can be granted read and cleared', function () {
    $user = createRoleControllerUser();
    $role = createRoleControllerRole();
    $menus = [
        createRoleControllerMenu(['name' => 'system:user']),
        createRoleControllerMenu(['name' => 'system:role']),
    ];
    $token = loginRoleControllerUser($user)['access_token'];
    $uri = "/admin/role/{$role->id}/permissions";

    $this->withToken($token)
        ->putJson($uri, ['permissions' => ['system:user', 'system:role']])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantRoleControllerPermission($user, 'permission:role:setMenu');
    grantRoleControllerPermission($user, 'permission:role:getMenu');

    $this->withToken($token)
        ->putJson($uri, ['permissions' => ['system:user', 'system:role']])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $permissions = $this->withToken($token)
        ->getJson($uri)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(2, 'data')
        ->json('data');

    expect(collect($permissions)->pluck('name')->sort()->values()->all())
        ->toBe(['system:role', 'system:user']);

    $this->withToken($token)
        ->putJson($uri, ['permissions' => []])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect($role->menus()->count())->toBe(0);
});
