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
use Mine\Annotation\Permission;
use Mine\Exception\NoPermissionException;
use Mine\Helper\LoginUser;
use Mine\MineRequest;

/**
 * Class PermissionAspect
 * @package Mine\Aspect
 */
#[Aspect]
class PermissionAspect extends AbstractAspect
{
    public array $annotations = [
        Permission::class
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
     * PermissionAspect constructor.
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
        if ($this->loginUser->isSuperAdmin()) {
            return $proceedingJoinPoint->process();
        }

        /** @var Permission $permission */
        if (isset($proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class])) {
            $permission = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class];
        }

        // 注解权限为空，则放行
        if (empty($permission->code)) {
            return $proceedingJoinPoint->process();
        }

        $this->checkPermission($permission->code, $permission->where);

        return $proceedingJoinPoint->process();
    }

    /**
     * 检查权限
     * @param string $codeString
     * @param string $where
     * @return bool
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function checkPermission(string $codeString, string $where): bool
    {
        $codes = $this->service->getInfo()['codes'];

        if ($where === 'OR') {
            foreach (explode(',', $codeString) as $code) {
                if (in_array(trim($code), $codes)) {
                    return true;
                }
            }
            throw new NoPermissionException(
                t('system.no_permission') . ' -> [ ' . $this->request->getPathInfo() . ' ]'
            );
        }

        if ($where === 'AND') {
            foreach (explode(',', $codeString) as $code) {
                $code = trim($code);
                if (! in_array($code, $codes)) {
                    $service = container()->get(\App\System\Service\SystemMenuService::class);
                    throw new NoPermissionException(
                        t('system.no_permission') . ' -> [ ' . $service->findNameByCode($code) . ' ]'
                    );
                }
            }
        }

        return true;
    }
}