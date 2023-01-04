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
namespace Mine\Aspect;

use App\System\Service\SystemUserService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;
use Mine\Annotation\Role;
use Mine\Exception\NoPermissionException;
use Mine\Helper\LoginUser;
use Mine\MineRequest;

/**
 * Class RoleAspect
 * @package Mine\Aspect
 */
#[Aspect]
class RoleAspect extends AbstractAspect
{
    public array $annotations = [
        Role::class
    ];

    /**
     * SystemUserService
     */
    protected SystemUserService $service;

    /**
     * MineRequest
     */
    protected MineRequest $request;

    /**
     * LoginUser
     */
    protected LoginUser $loginUser;

    /**
     * RoleAspect constructor.
     * @param SystemUserService $service
     * @param MineRequest $request
     * @param LoginUser $loginUser
     */
    public function __construct(
        SystemUserService $service,
        MineRequest $request,
        LoginUser $loginUser
    )
    {
        $this->service = $service;
        $this->request = $request;
        $this->loginUser = $loginUser;
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // 是超管角色放行
        if ($this->loginUser->isAdminRole()) {
            return $proceedingJoinPoint->process();
        }

        /** @var Role $role */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Role::class])) {
            $role = $proceedingJoinPoint->getAnnotationMetadata()->method[Role::class];
        }

        // 没有使用注解，则放行
        if (empty($role->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkRole($role->code, $role->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查角色
     * @param string $codeString
     * @param string $where
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkRole(string $codeString, string $where): bool
    {
        $roles = $this->service->getInfo()['roles'];

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $roles)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_role') . ' -> [ ' . $codeString . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $roles)) {
                    $service = container()->get(\App\System\Service\SystemRoleService::class);
                    throw new NoPermissionException(
                        t('system.no_role') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}