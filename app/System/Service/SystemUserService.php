<?php

declare(strict_types=1);
namespace App\System\Service;

use App\Setting\Service\SettingConfigService;
use App\System\Mapper\SystemUserMapper;
use App\System\Model\SystemUser;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Database\Model\ModelNotFoundException;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Redis\Redis;
use Mine\Abstracts\AbstractService;
use Mine\Event\UserLoginAfter;
use Mine\Event\UserLoginBefore;
use Mine\Event\UserLogout;
use Mine\Exception\CaptchaException;
use Mine\Exception\MineException;
use Mine\Exception\NormalStatusException;
use Mine\Exception\UserBanException;
use Mine\Helper\MineCaptcha;
use Mine\MineRequest;
use Mine\Helper\MineCode;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\SimpleCache\CacheInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * 用户业务
 * Class SystemUserService
 * @package App\System\Service
 */
class SystemUserService extends AbstractService
{
    /**
     * @var EventDispatcherInterface
     */
    #[InJect]
    protected EventDispatcherInterface $evDispatcher;

    /**
     * @var MineRequest
     */
    #[Inject]
    protected MineRequest $request;

    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * @var SystemMenuService
     */
    protected SystemMenuService $sysMenuService;

    /**
     * @var SystemRoleService
     */
    protected SystemRoleService $sysRoleService;

    /**
     * @var SystemUserMapper
     */
    public $mapper;

    /**
     * SystemUserService constructor.
     * @param ContainerInterface $container
     * @param SystemUserMapper $mapper
     * @param SystemMenuService $systemMenuService
     * @param SystemRoleService $systemRoleService
     */
    public function __construct(
        ContainerInterface $container,
        SystemUserMapper $mapper,
        SystemMenuService $systemMenuService,
        SystemRoleService $systemRoleService
    )
    {
        $this->mapper = $mapper;
        $this->sysMenuService = $systemMenuService;
        $this->sysRoleService = $systemRoleService;
        $this->container = $container;
    }

    /**
     * 获取验证码
     * @return string
     * @throws InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @noinspection PhpFullyQualifiedNameUsageInspection
     */
    public function getCaptcha(): string
    {
        $cache = $this->container->get(CacheInterface::class);
        $captcha = new MineCaptcha();
        $info = $captcha->getCaptchaInfo();
        $key = $this->request->ip() .'-'. \Mine\Helper\Str::lower($info['code']);
        $cache->set(sprintf('captcha:%s', $key), $info['code'], 60);
        return $info['image'];
    }

    /**
     * 检查用户提交的验证码
     * @param String $code
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Exception
     */
    public function checkCaptcha(String $code): bool
    {
        try {
            $cache = $this->container->get(CacheInterface::class);
            $key = 'captcha:' . $this->request->ip() .'-'. \Mine\Helper\Str::lower($code);
            $result = (\Mine\Helper\Str::lower($code) == $cache->get($key));
            $cache->delete($key);
            return $result;
        } catch (InvalidArgumentException $e) {
            throw new \Exception;
        }
    }

    /**
     * 用户登陆
     * @param array $data
     * @return string|null
     * @throws InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function login(array $data): ?string
    {
        try {
            $this->evDispatcher->dispatch(new UserLoginBefore($data));
            $userinfo = $this->mapper->checkUserByUsername($data['username'])->toArray();
            $password = $userinfo['password'];
            unset($userinfo['password']);
            $userLoginAfter = new UserLoginAfter($userinfo);
            $webLoginVerify = container()->get(SettingConfigService::class)->getConfigByKey('web_login_verify');
            if (isset($webLoginVerify['value']) && $webLoginVerify['value'] === '1') {
                if (! $this->checkCaptcha($data['code'])) {
                    $userLoginAfter->message = t('jwt.code_error');
                    $userLoginAfter->loginStatus = false;
                    $this->evDispatcher->dispatch($userLoginAfter);
                    throw new CaptchaException;
                }
            }
            if ($this->mapper->checkPass($data['password'], $password)) {
                if (
                    ($userinfo['status'] == SystemUser::USER_NORMAL)
                    ||
                    ($userinfo['status'] == SystemUser::USER_BAN && $userinfo['id'] == env('SUPER_ADMIN'))
                ) {
                    $userLoginAfter->message = t('jwt.login_success');
                    $token = user()->getToken($userLoginAfter->userinfo);
                    $userLoginAfter->token = $token;
                    $this->evDispatcher->dispatch($userLoginAfter);
                    return $token;
                } else {
                    $userLoginAfter->loginStatus = false;
                    $userLoginAfter->message = t('jwt.user_ban');
                    $this->evDispatcher->dispatch($userLoginAfter);
                    throw new UserBanException;
                }
            } else {
                $userLoginAfter->loginStatus = false;
                $userLoginAfter->message = t('jwt.password_error');
                $this->evDispatcher->dispatch($userLoginAfter);
                throw new NormalStatusException;
            }
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                throw new NormalStatusException(t('jwt.username_error'), MineCode::NO_DATA);
            }
            if ($e instanceof NormalStatusException) {
                throw new NormalStatusException(t('jwt.password_error'), MineCode::PASSWORD_ERROR);
            }
            if ($e instanceof UserBanException) {
                throw new NormalStatusException(t('jwt.user_ban'), MineCode::USER_BAN);
            }
            if ($e instanceof CaptchaException) {
                throw new NormalStatusException(t('jwt.code_error'));
            }
            console()->error($e->getMessage());
            throw new NormalStatusException(t('jwt.unknown_error'));
        }
    }

    /**
     * 用户退出
     * @throws InvalidArgumentException
     */
    public function logout()
    {
        $user = user();
        $this->evDispatcher->dispatch(new UserLogout($user->getUserInfo()));
        $user->getJwt()->logout();
    }

    /**
     * 获取用户信息
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getInfo(): array
    {
        if ( ($uid = user()->getId()) ) {
            return $this->getCacheInfo((int) $uid);
        }
        throw new MineException(t('system.unable_get_userinfo'), 500);
    }

    /**
     * 获取缓存用户信息
     * @param int $id
     * @return array
     */
    #[Cacheable(prefix: "loginInfo", ttl: 0, value: "userId_#{id}")]
    protected function getCacheInfo(int $id): array
    {
        $user = $this->mapper->getModel()->find($id);
        $user->addHidden('deleted_at', 'password');
        $data['user'] = $user->toArray();
        if (user()->isSuperAdmin()) {
            $data['roles'] = ['superAdmin'];
            $data['routers'] = $this->sysMenuService->mapper->getSuperAdminRouters();
            $data['codes'] = ['*'];
        } else {
            $roles = $this->sysRoleService->mapper->getMenuIdsByRoleIds($user->roles()->pluck('id')->toArray());
            $ids = $this->filterMenuIds($roles);
            $data['roles'] = $user->roles()->pluck('code')->toArray();
            $data['routers'] = $this->sysMenuService->mapper->getRoutersByIds($ids);
            $data['codes'] = $this->sysMenuService->mapper->getMenuCode($ids);
        }

        return $data;
    }

    /**
     * 过滤通过角色查询出来的菜单id列表，并去重
     * @param array $roleData
     * @return array
     */
    protected function filterMenuIds(array &$roleData): array
    {
        $ids = [];
        foreach ($roleData as $val) {
            foreach ($val['menus'] as $menu) {
                $ids[] = $menu['id'];
            }
        }
        unset($roleData);
        return array_unique($ids);
    }

    /**
     * 新增用户
     * @param array $data
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function save(array $data): int
    {
        if ($this->mapper->existsByUsername($data['username'])) {
            throw new NormalStatusException(t('system.username_exists'));
        } else {
            return $this->mapper->save($this->handleData($data));
        }
    }

    /**
     * 更新用户信息
     * @param int $id
     * @param array $data
     * @return bool
     */
    #[CacheEvict(prefix: "loginInfo", value: "userId_#{id}")]
    public function update(int $id, array $data): bool
    {
        if (isset($data['username'])) unset($data['username']);
        if (isset($data['password'])) unset($data['password']);
        return $this->mapper->update($id, $this->handleData($data));
    }

    /**
     * 处理提交数据
     * @param $data
     * @return array
     */
    protected function handleData($data): array
    {
        if (!is_array($data['role_ids'])) {
            $data['role_ids'] = explode(',', $data['role_ids']);
        }
        if (($key = array_search(env('ADMIN_ROLE'), $data['role_ids'])) !== false) {
            unset($data['role_ids'][$key]);
        }
        if (!empty($data['post_ids']) && !is_array($data['post_ids'])) {
            $data['post_ids'] = explode(',', $data['post_ids']);
        }
        if (is_array($data['dept_id'])) {
            $data['dept_id'] = array_pop($data['dept_id']);
        }
        return $data;
    }

    /**
     * 获取在线用户
     * @param array $params
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getOnlineUserPageList(array $params = []): array
    {
        $redis = redis();
        $key   = sprintf('%sToken:*', config('cache.default.prefix'));
        $users = $redis->keys($key);
        $userIds = [];

        foreach ($users as $user) {
            if ( preg_match("/{$key}(\d+)$/", $user, $match) && isset($match[1])) {
                $userIds[] = $match[1];
            }
        }

        if (empty($userIds)) {
            return [];
        }

        return $this->getPageList(array_merge([ 'showDept' => 1, 'userIds'  => $userIds ], $params));
    }

    /**
     * 删除用户
     * @param string $ids
     * @return bool
     */
    public function delete(string $ids): bool
    {
        if (!empty($ids)) {
            $userIds = explode(',', $ids);
            if (($key = array_search(env('SUPER_ADMIN'), $userIds)) !== false) {
                unset($userIds[$key]);
            }

            return $this->mapper->delete($userIds);
        }

        return false;
    }

    /**
     * 真实删除用户
     * @param string $ids
     * @return bool
     */
    public function realDelete(string $ids): bool
    {
        if (!empty($ids)) {
            $userIds = explode(',', $ids);
            if (($key = array_search(env('SUPER_ADMIN'), $userIds)) !== false) {
                unset($userIds[$key]);
            }

            return $this->mapper->realDelete($userIds);
        }

        return false;
    }

    /**
     * 强制下线用户
     * @param string $id
     * @return bool
     * @throws InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function kickUser(string $id): bool
    {
        $redis = redis();
        $key = sprintf("%sToken:%s", config('cache.default.prefix'), $id);
        if ($redis->exists($key)) {
            user()->getJwt()->logout($redis->get($key), 'default');
//            $redis->del($key);
        }
        return true;
    }

    /**
     * 初始化用户密码
     * @param int $id
     * @param string $password
     * @return bool
     */
    public function initUserPassword(int $id, string $password = '123456'): bool
    {
        return $this->mapper->initUserPassword($id, $password);
    }

    /**
     * 清除用户缓存
     * @param string $id
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function clearCache(string $id): bool
    {
        $redis = $this->container->get(Redis::class);
        $prefix = config('cache.default.prefix');
        return $redis->del("{$prefix}loginInfo:userId_{$id}") > 0;
    }

    /**
     * 设置用户首页
     * @param array $params
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setHomePage(array $params): bool
    {
        $res = ($this->mapper->getModel())::query()
            ->where('id', $params['id'])
            ->update(['dashboard' => $params['dashboard']]) > 0;

        $this->clearCache((string) $params['id']);
        return $res;
    }

    /**
     * 用户更新个人资料
     * @param array $params
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function updateInfo(array $params): bool
    {
        if (!isset($params['id'])) {
            return false;
        }

        $model = $this->mapper->getModel()::find($params['id']);
        unset($params['id'], $params['password']);
        foreach ($params as $key => $param) {
            $model[$key] = $param;
        }

        $this->clearCache((string) $model['id']);
        return $model->save();
    }

    /**
     * 用户修改密码
     * @param array $params
     * @return bool
     */
    public function modifyPassword(array $params): bool
    {
        return $this->mapper->initUserPassword((int) user()->getId(), $params['newPassword']);
    }
}