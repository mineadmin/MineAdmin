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
 * Class AuthAspect
 * @package Mine\Aspect
 * @Aspect
 */
class PermissionAspect extends AbstractAspect
{

    public $annotations = [
        Permission::class
    ];

    /**
     * @var SystemUserService
     */
    protected $service;

    /**
     * @var MineRequest
     */
    protected $request;

    /**
     * @var LoginUser
     */
    protected $loginUser;

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
        if (empty($permission->menuCode)) {
            return $proceedingJoinPoint->process();
        }

        // 获取当前用户权限列表
        $codes = $this->service->getInfo()['codes'];
        $pathInfo = $this->request->getPathInfo();
        if (!in_array($permission->menuCode, $codes)) {
            throw new NoPermissionException(t('system.no_permission') . ' -> ['. $pathInfo.']');
        }
        return $proceedingJoinPoint->process();
    }
}