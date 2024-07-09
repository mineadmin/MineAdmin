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

namespace App\System\Listener;

use App\System\Model\SystemLoginLog;
use App\System\Model\SystemUser;
use App\System\Service\SystemLoginLogService;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;
use Mine\Event\UserLoginAfter;
use Mine\Helper\Str;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Xmo\JWTAuth\JWT;

/**
 * Class LoginListener.
 */
#[Listener]
class LoginListener implements ListenerInterface
{
    public function listen(): array
    {
        return [
            UserLoginAfter::class,
        ];
    }

    /**
     * @param UserLoginAfter $event
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function process(object $event): void
    {
        $request = container()->get(MineRequest::class);
        $service = container()->get(SystemLoginLogService::class);
        $redis = redis();

        $agent = $request->getHeader('user-agent')[0] ?? 'unknown';
        $ip = $request->ip();
        $service->save([
            'username' => $event->userinfo['username'],
            'ip' => $ip,
            'ip_location' => Str::ipToRegion($ip),
            'os' => $this->os($agent),
            'browser' => $this->browser($agent),
            'status' => $event->loginStatus ? SystemLoginLog::SUCCESS : SystemLoginLog::FAIL,
            'message' => $event->message,
            'login_time' => date('Y-m-d H:i:s'),
        ]);

        if ($event->loginStatus && $event->token) {
            # 多点登录情况下，只保存一个key会导致最近时刻登录的用户如果登出之后,则该用户不会出现在在线用户监控列表中
            # 利用 token 设置jwt的jti 来做多点登录token的判断
            $jwt = container()->get(JWT::class);
            $parserData = $jwt->getParserData($event->token);
            $scene = $parserData['jwt_scene'];
            $config = $jwt->getSceneConfig($scene);
            $key = match ($config['login_type']) {
                'sso' => sprintf('%sToken:%s', config('cache.default.prefix'), $event->userinfo['id']),
                'mpop' => sprintf('%sToken:%s:%s', config('cache.default.prefix'), $event->userinfo['id'], $parserData['jti']),
            };
            $redis->del($key);
            $redis->set($key, $event->token, config('jwt.ttl'));
        }

        if ($event->loginStatus) {
            $event->userinfo['login_ip'] = $ip;
            $event->userinfo['login_time'] = date('Y-m-d H:i:s');

            SystemUser::query()->where('id', $event->userinfo['id'])->update([
                'login_ip' => $ip,
                'login_time' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * @param mixed $agent
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function os($agent): string
    {
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.1/i', $agent)) {
            return 'Windows 7';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 6.2/i', $agent)) {
            return 'Windows 8';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 10.0/i', $agent)) {
            return 'Windows 10';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 11.0/i', $agent)) {
            return 'Windows 11';
        }
        if (stripos($agent, 'win') !== false && preg_match('/nt 5.1/i', $agent)) {
            return 'Windows XP';
        }
        if (stripos($agent, 'linux') !== false) {
            return 'Linux';
        }
        if (stripos($agent, 'mac') !== false) {
            return 'Mac';
        }
        return t('jwt.unknown');
    }

    /**
     * @param mixed $agent
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function browser($agent): string
    {
        if (stripos($agent, 'MSIE') !== false) {
            return 'MSIE';
        }
        if (stripos($agent, 'Edg') !== false) {
            return 'Edge';
        }
        if (stripos($agent, 'Chrome') !== false) {
            return 'Chrome';
        }
        if (stripos($agent, 'Firefox') !== false) {
            return 'Firefox';
        }
        if (stripos($agent, 'Safari') !== false) {
            return 'Safari';
        }
        if (stripos($agent, 'Opera') !== false) {
            return 'Opera';
        }
        return t('jwt.unknown');
    }
}
