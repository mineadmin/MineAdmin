<?php
/**
 * MineAdmin is committed to providing solutions for quickly building web applications
 * Please view the LICENSE file that was distributed with this source code,
 * For the full copyright and license information.
 * Thank you very much for using MineAdmin.
 *
 * @Author X.Mo<root@imoi.cn>
 * @Link   https://gitee.com/xmo/MineAdmin
 */

declare(strict_types=1);
namespace Mine\Helper;

use Mine\Exception\TokenException;
use Mine\MineRequest;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Util\JWTUtil;
use Psr\SimpleCache\InvalidArgumentException;

class LoginUser
{
    /**
     * @var JWT
     */
    protected $jwt;

    /**
     * @var MineRequest
     */
    protected $request;


    /**
     * LoginUser constructor.
     * @param string $scene 场景，默认为default
     */
    public function __construct(string $scene = 'default')
    {
        /* @var JWT $this->jwt */
        $this->jwt = make(JWT::class)->setScene($scene);
    }

    /**
     * 验证token
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function check(): bool
    {
        try {
            if ($this->jwt->checkToken(null, true, true, true)) {
                return true;
            }
        } catch (InvalidArgumentException $e) {
            throw new TokenException(t('jwt.no_token'));
        } catch (\Throwable $e) {
            throw new TokenException(t('jwt.no_login'));
        }

        return false;
    }

    /**
     * 获取JWT对象
     * @return Jwt
     */
    public function getJwt(): Jwt
    {
        return $this->jwt;
    }

    /**
     * 获取当前登录用户信息
     * @return array
     */
    public function getUserInfo(): array
    {
        return $this->jwt->getParserData();
    }

    /**
     * 获取当前登录用户ID
     * @return string
     */
    public function getId(): string
    {
        return (string) $this->jwt->getParserData()['id'];
    }

    /**
     * 获取当前登录用户名
     * @return string
     */
    public function getUsername(): string
    {
        return $this->jwt->getParserData()['username'];
    }

    /**
     * 获取当前token用户角色
     * @return string
     */
    public function getRole(): string
    {
        return $this->jwt->getParserData()['role'];
    }

    /**
     * 获取当前token用户类型
     * @return string
     */
    public function getUserType(): string
    {
        return $this->jwt->getParserData()['user_type'];
    }

    /**
     * 获取当前token用户部门ID
     * @return string
     */
    public function getDeptId(): string
    {
        return $this->jwt->getParserData()['dept_id'];
    }

    /**
     * 是否为超级管理员（创始人），用户禁用对创始人没用
     * @return bool
     */
    public function isSuperAdmin():bool
    {
        return env('SUPER_ADMIN') == $this->getId();
    }

    /**
     * 是否为管理员角色
     * @return bool
     */
    public function isAdminRole(): bool
    {
        return env('ADMIN_ROLE') == $this->getRole();
    }

    /**
     * 获取Token
     * @param array $user
     * @return string
     * @throws InvalidArgumentException
     */
    public function getToken(array $user): string
    {
        return $this->jwt->getToken($user);
    }

    /**
     * 刷新token
     * @return string
     * @throws InvalidArgumentException
     */
    public function refresh(): string
    {
        return $this->jwt->refreshToken();
    }
}