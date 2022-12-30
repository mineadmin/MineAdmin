<?php

declare(strict_types=1);
namespace App\System\Service;

use App\System\Mapper\SystemUserMapper;
use Hyperf\Cache\Annotation\Cacheable;
use Hyperf\Cache\Annotation\CacheEvict;
use Hyperf\Contract\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Mine\Abstracts\AbstractService;
use Mine\Event\UserAdd;
use Mine\Event\UserDelete;
use Mine\Exception\MineException;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCaptcha;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
     */
    public function getCaptcha(): string
    {
        $cache = container()->get(CacheInterface::class);
        $captcha = new MineCaptcha();
        $info = $captcha->getCaptchaInfo();
        $key = $this->request->ip() .'-'. \Mine\Helper\Str::lower($info['code']);
        $cache->set(sprintf('captcha:%s', $key), $info['code'], 60);
        return $info['image'];
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
            $id = $this->mapper->save($this->handleData($data));
            $data['id'] = $id;
            event(new UserAdd($data));
            return $id;
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
        if (!empty($data['dept_ids']) && !is_array($data['dept_ids'])) {
            $data['dept_ids'] = explode(',', $data['dept_ids']);
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

        $userIds = [];
        $iterator = null;

        while (false !== ($users = $redis->scan($iterator, $key, 100))) {
            foreach ($users as $user) {
                if ( preg_match("/{$key}(\d+)$/", $user, $match) && isset($match[1])) {
                    $userIds[] = $match[1];
                }
            }
            unset($users);
        }

        if (empty($userIds)) {
            return [];
        }

        return $this->getPageList(array_merge(['userIds'  => $userIds ], $params));
    }

    /**
     * 删除用户
     * @param array $ids
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete(array $ids): bool
    {
        if (!empty($ids)) {
            if (($key = array_search(env('SUPER_ADMIN'), $ids)) !== false) {
                unset($ids[$key]);
            }
            $result = $this->mapper->delete($ids);
            event(new UserDelete($ids));
            return $result;
        }

        return false;
    }

    /**
     * 真实删除用户
     * @param array $ids
     * @return bool
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function realDelete(array $ids): bool
    {
        if (!empty($ids)) {
            if (($key = array_search(env('SUPER_ADMIN'), $ids)) !== false) {
                unset($ids[$key]);
            }
            $result = $this->mapper->realDelete($ids);
            event(new UserDelete($ids));
            return $result;
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
        user()->getJwt()->logout($redis->get($key), 'default');
        $redis->del($key);
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
        $redis = redis();
        $prefix = config('cache.default.prefix');

        $iterator = null;
        while (false !== ($configKey = $redis->scan($iterator, $prefix . 'config:*', 100))) {
            $redis->del($configKey);
        }
        while (false !== ($dictKey = $redis->scan($iterator, $prefix . 'Dict:*', 100))) {
            $redis->del($dictKey);
        }
        $redis->del([$prefix . 'crontab', $prefix . 'modules']);

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

    /**
     * 通过 id 列表获取用户基础信息
     */
    public function getUserInfoByIds(array $ids): array
    {
        return $this->mapper->getUserInfoByIds($ids);
    }
}