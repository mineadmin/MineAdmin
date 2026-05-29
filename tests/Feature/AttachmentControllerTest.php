<?php

use App\Http\Common\ResultCode;
use App\Models\Attachment;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\Permission\Menu;
use App\Models\Permission\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

function createAttachmentTestUser(array $attributes = []): User
{
    return User::factory()->create(array_merge(['password' => '123456', 'user_type' => Type::System, 'status' => Status::Normal], $attributes));
}

function loginAttachmentTestUser(User $user): array
{
    return test()->postJson('/admin/passport/login', ['username' => $user->username, 'password' => '123456'])->json('data');
}

function grantAttachmentPermission(User $user, string $permission): void
{
    $role = Role::query()->create(['name' => fake()->word(), 'code' => fake()->unique()->regexify('[A-Za-z0-9_]{10}'), 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);
    $menu = Menu::query()->create(['parent_id' => 0, 'name' => $permission, 'path' => '/'.fake()->unique()->word(), 'component' => 'default', 'redirect' => '', 'status' => Status::Normal, 'sort' => 1, 'remark' => '']);

    $role->menus()->sync([$menu->id]);
    $user->roles()->syncWithoutDetaching([$role->id]);
}

test('attachment list upload and delete endpoints work', function () {
    Storage::fake('public');

    $user = createAttachmentTestUser();
    $otherUser = createAttachmentTestUser();
    Attachment::query()->create(['origin_name' => 'mine.txt', 'storage_mode' => 'local', 'object_name' => 'mine.txt', 'hash' => 'hash1', 'suffix' => 'txt', 'size_byte' => 4, 'size_info' => '4 B', 'url' => '/storage/mine.txt', 'created_by' => $user->id]);
    Attachment::query()->create(['origin_name' => 'other.txt', 'storage_mode' => 'local', 'object_name' => 'other.txt', 'hash' => 'hash2', 'suffix' => 'txt', 'size_byte' => 5, 'size_info' => '5 B', 'url' => '/storage/other.txt', 'created_by' => $otherUser->id]);
    $token = loginAttachmentTestUser($user)['access_token'];

    $this->withToken($token)
        ->getJson('/admin/attachment/list')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Forbidden->value);

    foreach (['dataCenter:attachment:list', 'dataCenter:attachment:upload', 'dataCenter:attachment:delete'] as $permission) {
        grantAttachmentPermission($user, $permission);
    }

    $this->withToken($token)
        ->getJson('/admin/attachment/list?page=1&page_size=10')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.total', 1)
        ->assertJsonPath('data.list.0.origin_name', 'mine.txt');

    $upload = $this->withToken($token)
        ->postJson('/admin/attachment/upload', ['file' => UploadedFile::fake()->create('avatar.png', 1, 'image/png')])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.origin_name', 'avatar.png')
        ->assertJsonPath('data.created_by', $user->id)
        ->json('data');

    expect(Attachment::query()->whereKey($upload['id'])->exists())->toBeTrue();

    $this->withToken($token)
        ->deleteJson("/admin/attachment/{$upload['id']}")
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    expect(Attachment::query()->whereKey($upload['id'])->exists())->toBeFalse();
});
