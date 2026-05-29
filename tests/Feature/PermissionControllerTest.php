<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPermissionUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function createPermissionRole(array $attributes = []): Role
{
    return Role::query()->create(array_merge([
        'name' => fake()->word(),
        'code' => fake()->unique()->word(),
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

function createPermissionMenu(array $attributes = []): Menu
{
    return Menu::query()->create(array_merge([
        'parent_id' => 0,
        'name' => fake()->unique()->word(),
        'path' => '/'.fake()->unique()->word(),
        'component' => 'default',
        'redirect' => '',
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

test('permission endpoints require access token authentication', function () {
    $user = createPermissionUser();
    $refreshToken = loginPermissionUser($user)['refresh_token'];

    $this->getJson('/admin/permission/menus')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->get('/admin/permission/menus', [
        'Accept' => 'text/html',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value)
        ->assertJsonStructure(['code', 'message', 'data']);

    $this->get('/api/user', [
        'Accept' => 'text/html',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value)
        ->assertJsonStructure(['code', 'message', 'data']);

    $this->withToken($refreshToken)
        ->getJson('/admin/permission/menus')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($refreshToken)
        ->getJson('/admin/permission/roles')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($refreshToken)
        ->postJson('/admin/permission/update')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);
});

test('menus returns all root normal menus for super admin', function () {
    $user = createPermissionUser();
    $role = createPermissionRole(['code' => 'SuperAdmin']);
    $user->roles()->sync([$role->id]);
    $root = createPermissionMenu(['name' => 'dashboard', 'sort' => 1]);
    $child = createPermissionMenu(['parent_id' => $root->id, 'name' => 'dashboard.child', 'sort' => 1]);
    createPermissionMenu(['name' => 'disabled', 'status' => Status::Disable]);
    $token = loginPermissionUser($user)['access_token'];

    $this->withToken($token)
        ->getJson('/admin/permission/menus')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $root->id)
        ->assertJsonPath('data.0.children.0.id', $child->id);
});

test('menus returns assigned normal role menus for current user', function () {
    $user = createPermissionUser();
    $role = createPermissionRole();
    $root = createPermissionMenu(['name' => 'system', 'sort' => 1]);
    $child = createPermissionMenu(['parent_id' => $root->id, 'name' => 'system.user', 'sort' => 2]);
    createPermissionMenu(['name' => 'unassigned']);
    $role->menus()->sync([$root->id, $child->id]);
    $user->roles()->sync([$role->id]);
    $token = loginPermissionUser($user)['access_token'];

    $this->withToken($token)
        ->getJson('/admin/permission/menus')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $root->id)
        ->assertJsonPath('data.0.children.0.id', $child->id);
});

test('roles returns all normal roles for super admin and own roles for normal user', function () {
    $superAdmin = createPermissionUser();
    $normalUser = createPermissionUser();
    $superAdminRole = createPermissionRole(['code' => 'SuperAdmin', 'sort' => 1]);
    $normalRole = createPermissionRole(['name' => 'Operator', 'code' => 'operator', 'remark' => 'ops', 'sort' => 2]);
    createPermissionRole(['code' => 'disabled', 'status' => Status::Disable]);
    $superAdmin->roles()->sync([$superAdminRole->id]);
    $normalUser->roles()->sync([$normalRole->id]);

    $this->withToken(loginPermissionUser($superAdmin)['access_token'])
        ->getJson('/admin/permission/roles')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(2, 'data');

    $this->withToken(loginPermissionUser($normalUser)['access_token'])
        ->getJson('/admin/permission/roles')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.name', 'Operator')
        ->assertJsonPath('data.0.code', 'operator')
        ->assertJsonPath('data.0.remark', 'ops')
        ->assertJsonMissingPath('data.0.id');
});

test('update changes profile fields and password', function () {
    $user = createPermissionUser();
    $token = loginPermissionUser($user)['access_token'];

    $this->withToken($token)
        ->post('/admin/permission/update', [
            'new_password' => 'short',
            'new_password_confirmation' => 'short',
        ], [
            'Accept' => 'text/html',
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonPath('message', __('validation.min.string', ['attribute' => __('user.password'), 'min' => 8]))
        ->assertJsonStructure(['code', 'message', 'data' => ['new_password']]);

    $this->withToken($token)
        ->postJson('/admin/permission/update', [
            'nickname' => 'Mine Admin',
            'avatar' => 'avatar.png',
            'signed' => 'hello',
            'backend_setting' => ['theme' => 'dark'],
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $user->refresh();
    expect($user->nickname)->toBe('Mine Admin')
        ->and($user->avatar)->toBe('avatar.png')
        ->and($user->signed)->toBe('hello')
        ->and($user->backend_setting)->toBe(['theme' => 'dark']);

    $this->withToken($token)
        ->postJson('/admin/permission/update', [
            'old_password' => 'wrong-password',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value);

    $this->withToken($token)
        ->postJson('/admin/permission/update', [
            'old_password' => '123456',
            'new_password' => 'new-password',
            'new_password_confirmation' => 'new-password',
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect($user->refresh()->verifyPassword('new-password'))->toBeTrue();
});

function loginPermissionUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}
