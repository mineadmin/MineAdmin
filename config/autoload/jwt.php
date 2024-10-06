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
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\RegisteredClaims;
use Mine\Jwt\Jwt;

return [
    'default' => [
        // jwt 配置 https://lcobucci-jwt.readthedocs.io/en/latest/
        'driver' => Jwt::class,
        // jwt 签名key
        'key' => InMemory::base64Encoded(env('JWT_SECRET')),
        // jwt 签名算法 可选 https://lcobucci-jwt.readthedocs.io/en/latest/supported-algorithms/
        'alg' => new Sha256(),
        // token过期时间，单位为秒
        'ttl' => (int) env('JWT_TTL', 3600),
        // 刷新token过期时间，单位为秒
        'refresh_ttl' => (int) env('JWT_REFRESH_TTL', 7200),
        // 黑名单模式
        'blacklist' => [
            // 是否开启黑名单
            'enable' => true,
            // 黑名单缓存前缀
            'prefix' => 'jwt_blacklist',
            // 黑名单缓存驱动
            'connection' => 'default',
            // 黑名单缓存时间 该时间一定要设置比token过期时间要大一点，最好设置跟过期时间一样
            'ttl' => (int) env('JWT_BLACKLIST_TTL', 7201),
        ],
        'claims' => [
            // 默认的jwt claims
            RegisteredClaims::ISSUER => (string) env('APP_NAME'),
        ],
    ],
    // 在你想要使用不同的场景时，可以在这里添加配置.可以填一个。其他会使用默认配置
    'application' => [
        // jwt 配置 https://lcobucci-jwt.readthedocs.io/en/latest/
    ],
];
