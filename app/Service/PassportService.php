<?php

namespace App\Service;

use App\Exception\BusinessException;
use App\Http\Common\ResultCode;
use App\Models\Enums\User\Type;
use App\Models\User;
use App\Models\UserLoginLog;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class PassportService
{
    public function login(string $username, string $password, Type $userType = Type::System, string $ip = '0.0.0.0', string $browser = 'unknown', string $os = 'unknown'): array
    {
        $user = User::query()
            ->where('username', $username)
            ->where('user_type', $userType->value)
            ->first();

        if ($user === null) {
            throw new BusinessException(ResultCode::NotFound, 'Not Found');
        }

        if (! $user->verifyPassword($password)) {
            $this->recordLogin($user->username, $ip, $browser, $os, false);

            throw new BusinessException(ResultCode::Unprocessable, trans('auth.password_error'));
        }

        if ($user->status->isDisable()) {
            throw new BusinessException(ResultCode::Disabled, trans('user.disable'));
        }

        $this->recordLogin($user->username, $ip, $browser, $os, true);

        return $this->issueTokens($user);
    }

    public function logout(?string $token): void
    {
        $this->syncToken($token);
        $guard = $this->guard();

        if ($guard->getPayload()->get('token_type') !== 'access') {
            throw new BusinessException(ResultCode::Unauthorized, trans('auth.unauthenticated'));
        }

        $guard->logout();
    }

    public function refresh(?string $token): array
    {
        $this->syncToken($token);
        $guard = $this->guard();
        $payload = $guard->getPayload();

        if ($payload->get('token_type') !== 'refresh') {
            throw new BusinessException(ResultCode::Unauthorized, trans('auth.unauthenticated'));
        }

        $userId = $payload->get('sub');
        $guard->invalidate();

        /** @var User $user */
        $user = User::query()->findOrFail($userId);

        return $this->issueTokens($user);
    }

    /**
     * @return array{access_token: string, refresh_token: string, expire_at: int}
     */
    private function recordLogin(string $username, string $ip, string $browser, string $os, bool $successful): void
    {
        UserLoginLog::query()->create([
            'username' => $username,
            'ip' => $ip,
            'os' => $os,
            'browser' => $browser,
            'status' => $successful ? 1 : 2,
        ]);
    }

    private function issueTokens(User $user): array
    {
        $guard = $this->guard();
        $accessToken = $guard->claims(['token_type' => 'access'])->login($user);
        $refreshToken = $guard->setTTL((int) config('jwt.refresh_ttl'))->claims(['token_type' => 'refresh'])->login($user);
        $guard->setTTL((int) config('jwt.ttl'));
        $guard->forgetUser();
        $guard->unsetToken();

        return [
            /** 授权 token */
            'access_token' => $accessToken,
            /** 刷新 token */
            'refresh_token' => $refreshToken,
            /** 过期时间 单位（秒） */
            'expire_at' => (int) config('jwt.ttl') * 60,
        ];
    }

    private function guard(): JWTGuard
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');

        return $guard;
    }

    private function syncToken(?string $token): void
    {
        $this->guard()->forgetUser();
        $this->guard()->unsetToken();

        if ($token !== null) {
            $this->guard()->setToken($token);
        }
    }
}
