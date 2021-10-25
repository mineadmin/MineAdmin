<?php

declare(strict_types=1);
namespace Mine\Aspect;

use App\System\Service\SystemMenuService;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\Event\Operation;
use Mine\Helper\LoginUser;
use Mine\Helper\Str;
use Mine\MineRequest;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Class OperationLogAspect
 * @package Mine\Aspect
 * @Aspect
 */
class OperationLogAspect extends AbstractAspect
{
    public $annotations = [
        OperationLog::class
    ];

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct()
    {
        $this->container = container();
    }

    /**
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed|void
     * @throws \Hyperf\Di\Exception\Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        /** @var Permission $permission */
        $permission = $proceedingJoinPoint->getAnnotationMetadata()->method[Permission::class];
        $result = $proceedingJoinPoint->process();
        $evDispatcher = $this->container->get(EventDispatcherInterface::class);
        $evDispatcher->dispatch(new Operation($this->getRequestInfo([
            'code' => $permission->menuCode,
            'response_code' => $result->getStatusCode(),
            'response_data' => $result->getBody()->getContents()
        ])));
        return $result;
    }

    /**
     * @param array $data
     * @return array

     */
    protected function getRequestInfo(array $data): array
    {
        $request = $this->container->get(MineRequest::class);
        $loginUser = $this->container->get(LoginUser::class);

        $operationLog = [
            'time' => date('Y-m-d H:i:s', $request->getServerParams()['request_time']),
            'method' => $request->getServerParams()['request_method'],
            'router' => $request->getServerParams()['path_info'],
            'protocol' => $request->getServerParams()['server_protocol'],
            'ip' => $request->ip(),
            'ip_location' => Str::ipToRegion($request->ip()),
            'service_name' => $this->getOperationMenuName($data['code']),
            'request_data' => $request->all(),
            'response_code' => $data['response_code'],
            'response_data' => $data['response_data'],
        ];
        try {
            $operationLog['username'] = $loginUser->getUsername();
        } catch (\Exception $e) {
            $operationLog['username'] = t('system.no_login_user');
        }

        return $operationLog;
    }

    /**
     * 获取菜单名称
     * @param string $code
     * @return string
     */
    protected function getOperationMenuName(string $code): string
    {
        return $this->container->get(SystemMenuService::class)->findNameByCode($code);
    }
}