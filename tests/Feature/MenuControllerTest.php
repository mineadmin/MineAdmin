<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createMenuControllerUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
    ], $attributes));
}

function createMenuControllerRole(array $attributes = []): Role
{
    return Role::query()->create(array_merge([
        'name' => fake()->word(),
        'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'),
        'status' => Status::Normal,
        'sort' => fake()->numberBetween(1, 100),
        'remark' => '',
    ], $attributes));
}

function createMenuControllerMenu(array $attributes = []): Menu
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

function loginMenuControllerUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}

function grantMenuControllerPermission(User $user, string $permission): void
{
    $role = createMenuControllerRole();
    $menu = createMenuControllerMenu(['name' => $permission, 'sort' => 9999]);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('menu list requires permission and returns root tree', function () {
    $user = createMenuControllerUser();
    $root = createMenuControllerMenu(['name' => 'system', 'sort' => 2]);
    $child = createMenuControllerMenu(['parent_id' => $root->id, 'name' => 'system.user', 'sort' => 1]);
    createMenuControllerMenu(['name' => 'dashboard', 'sort' => 1]);
    createMenuControllerMenu(['parent_id' => $root->id, 'name' => 'system.disabled', 'status' => Status::Disable]);
    $token = loginMenuControllerUser($user)['access_token'];

    $this->getJson('/admin/menu/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->getJson('/admin/menu/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantMenuControllerPermission($user, 'permission:menu:index');

    $this->withToken($token)
        ->getJson('/admin/menu/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonCount(3, 'data')
        ->assertJsonPath('data.0.name', 'dashboard')
        ->assertJsonPath('data.1.name', 'system')
        ->assertJsonPath('data.1.children.0.id', $child->id)
        ->assertJsonCount(1, 'data.1.children');
});

test('menu create validates stores creator and creates button permissions', function () {
    $user = createMenuControllerUser();
    $token = loginMenuControllerUser($user)['access_token'];
    $payload = [
        'parent_id' => 0,
        'name' => 'system:user',
        'path' => '/system/user',
        'component' => 'modules/system/user/index',
        'redirect' => '',
        'status' => Status::Normal->value,
        'sort' => 10,
        'remark' => 'users',
        'meta' => [
            'title' => '用户管理',
            'i18n' => 'menu.system.user',
            'type' => 'M',
        ],
        'btnPermission' => [
            [
                'code' => 'permission:user:create',
                'title' => '创建用户',
                'i18n' => 'permission.user.create',
            ],
        ],
    ];

    $this->withToken($token)
        ->postJson('/admin/menu', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantMenuControllerPermission($user, 'permission:menu:create');

    $this->withToken($token)
        ->postJson('/admin/menu', [])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['name', 'meta.title']]);

    $this->withToken($token)
        ->postJson('/admin/menu', $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $menu = Menu::query()->where('name', 'system:user')->first();
    $button = Menu::query()->where('name', 'permission:user:create')->first();

    expect($menu)->not->toBeNull()
        ->and($menu->created_by)->toBe($user->id)
        ->and($menu->meta->title)->toBe('用户管理')
        ->and($button)->not->toBeNull()
        ->and($button->parent_id)->toBe($menu->id)
        ->and($button->status)->toBe(Status::Normal)
        ->and($button->meta->type)->toBe('B')
        ->and($button->meta->title)->toBe('创建用户');
});

test('menu update stores updater and syncs button permissions', function () {
    $user = createMenuControllerUser();
    $menu = createMenuControllerMenu([
        'name' => 'system:user',
        'meta' => ['title' => '用户管理', 'type' => 'M'],
    ]);
    $existingButton = createMenuControllerMenu([
        'parent_id' => $menu->id,
        'name' => 'permission:user:create',
        'meta' => ['title' => '创建用户', 'i18n' => 'permission.user.create', 'type' => 'B'],
    ]);
    $staleButton = createMenuControllerMenu([
        'parent_id' => $menu->id,
        'name' => 'permission:user:delete',
        'meta' => ['title' => '删除用户', 'i18n' => 'permission.user.delete', 'type' => 'B'],
    ]);
    $otherMenu = createMenuControllerMenu(['name' => 'system:role']);
    $otherButton = createMenuControllerMenu([
        'parent_id' => $otherMenu->id,
        'name' => 'permission:role:create',
        'meta' => ['title' => '创建角色', 'i18n' => 'permission.role.create', 'type' => 'B'],
    ]);
    $token = loginMenuControllerUser($user)['access_token'];
    $payload = [
        'parent_id' => 0,
        'name' => 'system:user:update',
        'path' => '/system/user',
        'component' => 'modules/system/user/index',
        'redirect' => '',
        'status' => Status::Disable->value,
        'sort' => 20,
        'remark' => 'updated',
        'meta' => [
            'title' => '用户列表',
            'i18n' => 'menu.system.user',
            'type' => 'M',
        ],
        'btnPermission' => [
            [
                'id' => $existingButton->id,
                'type' => 'B',
                'code' => 'permission:user:create-updated',
                'title' => '创建用户更新',
                'i18n' => 'permission.user.create.updated',
            ],
            [
                'id' => $otherButton->id,
                'type' => 'B',
                'code' => 'permission:role:hijacked',
                'title' => '劫持角色',
                'i18n' => 'permission.role.hijacked',
            ],
            [
                'type' => 'B',
                'code' => 'permission:user:export',
                'title' => '导出用户',
                'i18n' => 'permission.user.export',
            ],
        ],
    ];

    $this->withToken($token)
        ->putJson("/admin/menu/{$menu->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantMenuControllerPermission($user, 'permission:menu:save');

    $this->withToken($token)
        ->putJson("/admin/menu/{$menu->id}", $payload)
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $menu->refresh();
    $existingButton->refresh();
    $otherButton->refresh();
    $newButton = Menu::query()->where('name', 'permission:user:export')->first();

    expect($menu->name)->toBe('system:user:update')
        ->and($menu->updated_by)->toBe($user->id)
        ->and($menu->status)->toBe(Status::Disable)
        ->and($existingButton->name)->toBe('permission:user:create-updated')
        ->and($existingButton->meta->title)->toBe('创建用户更新')
        ->and($otherButton->name)->toBe('permission:role:create')
        ->and($otherButton->meta->title)->toBe('创建角色')
        ->and(Menu::query()->whereKey($staleButton->id)->exists())->toBeFalse()
        ->and($newButton)->not->toBeNull()
        ->and($newButton->parent_id)->toBe($menu->id)
        ->and($newButton->meta->type)->toBe('B');
});

test('menu delete requires permission and detaches role relations', function () {
    $user = createMenuControllerUser();
    $menu = createMenuControllerMenu();
    $role = createMenuControllerRole();
    $role->menus()->sync([$menu->id]);
    $token = loginMenuControllerUser($user)['access_token'];

    $this->deleteJson('/admin/menu')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->deleteJson('/admin/menu', [$menu->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    grantMenuControllerPermission($user, 'permission:menu:delete');

    $this->withToken($token)
        ->deleteJson('/admin/menu', [$menu->id])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Menu::query()->whereKey($menu->id)->exists())->toBeFalse()
        ->and($role->menus()->whereKey($menu->id)->exists())->toBeFalse();
});
