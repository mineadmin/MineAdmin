<?php

use App\Models\Attachment;
use App\Models\Casts\MetaCast;
use App\Models\DataPermission\Policy;
use App\Models\Enums\DataPermission\PolicyType;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Department;
use App\Models\Permission\Leader;
use App\Models\Permission\Menu;
use App\Models\Permission\Meta;
use App\Models\Permission\Position;
use App\Models\Permission\Role;
use App\Models\User;
use App\Models\UserLoginLog;
use App\Models\UserOperationLog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Tests\TestCase;

uses(TestCase::class);

function classAttribute(string $class, string $attribute): object
{
    $attributes = (new ReflectionClass($class))->getAttributes($attribute);

    expect($attributes)->toHaveCount(1);

    return $attributes[0]->newInstance();
}

test('migrated models expose the expected table names', function () {
    expect((new User)->getTable())->toBe('user')
        ->and((new Attachment)->getTable())->toBe('attachment')
        ->and((new UserLoginLog)->getTable())->toBe('user_login_log')
        ->and((new UserOperationLog)->getTable())->toBe('user_operation_log')
        ->and((new Policy)->getTable())->toBe('data_permission_policy')
        ->and((new Department)->getTable())->toBe('department')
        ->and((new Leader)->getTable())->toBe('dept_leader')
        ->and((new Menu)->getTable())->toBe('menu')
        ->and((new Position)->getTable())->toBe('position')
        ->and((new Role)->getTable())->toBe('role');
});

test('migrated models use fillable attributes', function (string $class, array $expected) {
    $fillable = classAttribute($class, Fillable::class);

    expect($fillable->columns)->toBe($expected)
        ->and((new $class)->getFillable())->toBe($expected);
})->with([
    'user' => [User::class, ['id', 'username', 'password', 'user_type', 'nickname', 'phone', 'email', 'avatar', 'signed', 'status', 'login_ip', 'login_time', 'backend_setting', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark']],
    'attachment' => [Attachment::class, ['id', 'storage_mode', 'origin_name', 'object_name', 'hash', 'mime_type', 'storage_path', 'suffix', 'size_byte', 'size_info', 'url', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark']],
    'user login log' => [UserLoginLog::class, ['id', 'username', 'ip', 'os', 'browser', 'status', 'message', 'login_time', 'remark']],
    'user operation log' => [UserOperationLog::class, ['id', 'username', 'method', 'router', 'service_name', 'ip', 'ip_location', 'created_at', 'updated_at', 'remark']],
    'policy' => [Policy::class, ['id', 'user_id', 'position_id', 'policy_type', 'is_default', 'created_at', 'updated_at', 'deleted_at', 'value']],
    'department' => [Department::class, ['id', 'name', 'parent_id', 'created_at', 'updated_at', 'deleted_at']],
    'leader' => [Leader::class, ['user_id', 'dept_id', 'created_at', 'updated_at', 'deleted_at']],
    'menu' => [Menu::class, ['id', 'parent_id', 'name', 'component', 'redirect', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark', 'meta', 'path']],
    'meta' => [Meta::class, ['title', 'i18n', 'badge', 'icon', 'affix', 'hidden', 'type', 'cache', 'copyright', 'useDefaultLayout', 'breadcrumbEnable', 'componentPath', 'componentSuffix', 'link', 'activeName', 'auth', 'role', 'user']],
    'position' => [Position::class, ['id', 'name', 'dept_id', 'created_at', 'updated_at', 'deleted_at']],
    'role' => [Role::class, ['id', 'name', 'code', 'data_scope', 'status', 'sort', 'created_by', 'updated_by', 'created_at', 'updated_at', 'remark']],
]);

test('user hides the password attribute', function () {
    $hidden = classAttribute(User::class, Hidden::class);

    expect($hidden->columns)->toBe(['password'])
        ->and((new User)->getHidden())->toBe(['password']);
});

test('migrated models cast enum and json attributes', function () {
    $user = new User([
        'status' => Status::Normal,
        'user_type' => Type::System,
        'backend_setting' => ['theme' => 'dark'],
    ]);
    $menu = new Menu(['status' => Status::Normal]);
    $policy = new Policy(['policy_type' => PolicyType::DeptSelf, 'value' => ['dept_id' => 1]]);

    expect($user->status)->toBe(Status::Normal)
        ->and($user->user_type)->toBe(Type::System)
        ->and($user->backend_setting)->toBe(['theme' => 'dark'])
        ->and($menu->status)->toBe(Status::Normal)
        ->and($policy->policy_type)->toBe(PolicyType::DeptSelf)
        ->and($policy->value)->toBe(['dept_id' => 1]);
});

test('user password mutator hashes and verifies passwords', function () {
    $user = new User(['password' => 'secret']);

    expect($user->password)->not->toBe('secret')
        ->and($user->verifyPassword('secret'))->toBeTrue()
        ->and($user->verifyPassword('invalid'))->toBeFalse();
});

test('meta cast converts json into meta models and serializes attributes', function () {
    $cast = new MetaCast;
    $model = new Menu;

    $meta = $cast->get($model, 'meta', '{"title":"Dashboard","hidden":false}', []);
    $serialized = $cast->set($model, 'meta', new Meta(['title' => 'Dashboard', 'hidden' => false]), []);

    expect($meta)->toBeInstanceOf(Meta::class)
        ->and($meta->title)->toBe('Dashboard')
        ->and(json_decode($serialized, true, flags: JSON_THROW_ON_ERROR))->toBe([
            'title' => 'Dashboard',
            'hidden' => false,
        ]);
});

test('migrated relationships point at the expected tables', function () {
    expect((new User)->roles())->toBeInstanceOf(BelongsToMany::class)
        ->and((new User)->department())->toBeInstanceOf(BelongsToMany::class)
        ->and((new User)->policy())->toBeInstanceOf(HasOne::class)
        ->and((new Role)->menus())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Role)->users())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Menu)->roles())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Menu)->children())->toBeInstanceOf(HasMany::class)
        ->and((new Department)->positions())->toBeInstanceOf(HasMany::class)
        ->and((new Department)->departmentUsers())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Leader)->department())->toBeInstanceOf(BelongsTo::class)
        ->and((new Leader)->user())->toBeInstanceOf(BelongsTo::class)
        ->and((new Position)->users())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Position)->policy())->toBeInstanceOf(HasOne::class)
        ->and((new Policy)->positions())->toBeInstanceOf(BelongsToMany::class)
        ->and((new Policy)->users())->toBeInstanceOf(BelongsToMany::class);
});
