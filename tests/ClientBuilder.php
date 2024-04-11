<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace HyperfTests;

use App\System\Model\SystemUser;
use Hyperf\Redis\Redis;
use Hyperf\Testing\Client;
use Xmo\JWTAuth\JWT;

/**
 * Class HttpTestCase.
 * @method static get($uri, $data = [], $headers = [])
 * @method static post($uri, $data = [], $headers = [])
 * @method static put($uri, $data = [], $headers = [])
 * @method static delete($uri, $data = [], $headers = [])
 * @method static json($uri, $data = [], $headers = [])
 * @method static file($uri, $data = [], $headers = [])
 * @method static request($method, $path, $options = [])
 */
class ClientBuilder
{
    public static function __callStatic($name, $arguments)
    {
        $count = count($arguments);
        if (! isset($arguments[2])) {
            if ($count === 1) {
                $arguments[1] = [];
            }
            $arguments[2] = [
                'Authorization' => 'Bearer ' . static::getBearToken(),
            ];
        }
        return static::makeClient()->{$name}(...$arguments);
    }

    public static function getBearToken(): ?string
    {
        $jwt = make(JWT::class);
        $user = SystemUser::query()->whereKey(env('SUPER_ADMIN', 1))->first();
        $token = $jwt->getToken($user->toArray());
        $key = sprintf('%sToken:%s', config('cache.default.prefix'), $user->id);
        $redis = make(Redis::class);
        $redis->set($key, $token);
        usleep(100);
        return $token;
    }

    public static function makeClient(): Client
    {
        return make(Client::class);
    }
}
