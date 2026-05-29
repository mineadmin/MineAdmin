<?php

use App\Http\Common\ResultCode;
use App\Models\Enums\User\Status;
use App\Models\Enums\User\Type;
use App\Models\User;
use App\Models\UserLoginLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createPassportUser(array $attributes = []): User
{
    return User::factory()->create(array_merge([
        'password' => '123456',
        'user_type' => Type::System,
        'status' => Status::Normal,
        'backend_setting' => ['theme' => 'dark'],
    ], $attributes));
}

test('login validates missing username', function () {
    $this->postJson('/admin/passport/login', [
        'username' => 'missing-user',
        'password' => '123456',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonStructure(['code', 'message', 'data' => ['username']]);
});

test('admin validation errors always use result response', function () {
    $this->post('/admin/passport/login', [
        'password' => '123456',
    ], [
        'Accept' => 'text/html',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonPath('message', __('validation.required', ['attribute' => __('user.username')]))
        ->assertJsonStructure(['code', 'message', 'data' => ['username']]);
});

test('login rejects wrong password and records failed login log', function () {
    $user = createPassportUser();

    $this->withServerVariables(['HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X)'])
        ->postJson('/admin/passport/login', [
            'username' => $user->username,
            'password' => 'wrong-password',
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unprocessable->value)
        ->assertJsonPath('message', '用户名或密码错误。');

    $log = UserLoginLog::query()->where('username', $user->username)->first();

    expect($log)->not->toBeNull()
        ->and($log->status)->toBe(2)
        ->and($log->ip)->not->toBeNull()
        ->and($log->os)->not->toBeNull()
        ->and($log->browser)->not->toBeNull();
});

test('login rejects non system users', function () {
    $user = createPassportUser(['user_type' => Type::User]);

    $this->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::NotFound->value);
});

test('login rejects disabled users', function () {
    $user = createPassportUser(['status' => Status::Disable]);

    $this->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Disabled->value)
        ->assertJsonPath('message', trans('user.disable'));
});

test('login returns jwt tokens and records successful login log', function () {
    $user = createPassportUser();

    $this->withServerVariables(['HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0)'])
        ->postJson('/admin/passport/login', [
            'username' => $user->username,
            'password' => '123456',
        ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonStructure([
            'code',
            'message',
            'data' => ['access_token', 'refresh_token', 'expire_at'],
        ])
        ->assertJsonPath('data.expire_at', config('jwt.ttl') * 60);

    $log = UserLoginLog::query()->where('username', $user->username)->first();

    expect($log)->not->toBeNull()
        ->and($log->status)->toBe(1)
        ->and($log->ip)->not->toBeNull()
        ->and($log->os)->not->toBeNull()
        ->and($log->browser)->not->toBeNull();
});

test('logout requires authentication', function () {
    $this->postJson('/admin/passport/logout')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);
});

test('admin authentication errors always use result response', function () {
    $this->post('/admin/passport/logout', [], [
        'Accept' => 'text/html',
    ])
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value)
        ->assertJsonStructure(['code', 'message', 'data']);
});

test('logout invalidates access token', function () {
    $user = createPassportUser();
    $login = loginPassportUser($user);
    $token = $login['access_token'];

    $this->withToken($login['refresh_token'])
        ->postJson('/admin/passport/logout')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->postJson('/admin/passport/logout')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $this->withToken($token)
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);
});

test('get info returns current user profile fields', function () {
    $user = createPassportUser([
        'nickname' => 'Mine Admin',
        'avatar' => 'avatar.png',
        'signed' => 'hello',
        'phone' => '16858888988',
        'email' => 'admin@example.com',
    ]);
    $token = loginPassportUser($user)['access_token'];

    $this->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($token)
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonPath('data.username', $user->username)
        ->assertJsonPath('data.nickname', 'Mine Admin')
        ->assertJsonPath('data.avatar', 'avatar.png')
        ->assertJsonPath('data.signed', 'hello')
        ->assertJsonPath('data.phone', '16858888988')
        ->assertJsonPath('data.email', 'admin@example.com')
        ->assertJsonPath('data.backend_setting', ['theme' => 'dark'])
        ->assertJsonMissingPath('data.password');
});

test('refresh returns new tokens and keeps original access token valid', function () {
    $user = createPassportUser();
    $login = loginPassportUser($user);

    $this->withToken($login['access_token'])
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $this->withToken($login['access_token'])
        ->postJson('/admin/passport/refresh')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $this->withToken($login['refresh_token'])
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Unauthorized->value);

    $refresh = $this->withToken($login['refresh_token'])
        ->postJson('/admin/passport/refresh')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value)
        ->assertJsonStructure([
            'code',
            'message',
            'data' => ['access_token', 'refresh_token', 'expire_at'],
        ])
        ->json('data');

    expect($refresh['access_token'])->not->toBe($login['access_token'])
        ->and($refresh['refresh_token'])->not->toBe($login['refresh_token'])
        ->and($refresh['expire_at'])->toBe(config('jwt.ttl') * 60);

    $this->withToken($login['access_token'])
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);

    $this->withToken($refresh['access_token'])
        ->getJson('/admin/passport/getInfo')
        ->assertSuccessful()
        ->assertJsonPath('code', ResultCode::Success->value);
});

function loginPassportUser(User $user): array
{
    return test()->postJson('/admin/passport/login', [
        'username' => $user->username,
        'password' => '123456',
    ])->json('data');
}
