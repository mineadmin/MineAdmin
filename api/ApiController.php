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
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace Api;

use Api\Middleware\VerifyInterfaceMiddleware;
use App\System\Service\SystemAppService;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\MultipleAnnotation;
use Hyperf\Di\ReflectionManager;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Annotation\Scene;
use Hyperf\Validation\Request\FormRequest;
use Hyperf\Validation\ValidationException;
use Mine\Exception\NoPermissionException;
use Mine\Exception\NormalStatusException;
use Mine\Exception\TokenException;
use Mine\Helper\MineCode;
use Mine\MineApi;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class ApiController.
 */
#[Controller(prefix: 'api')]
class ApiController extends MineApi
{
    public const SIGN_VERSION = '1.0';

    /**
     * 初始化.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function __init()
    {
        if (empty($this->request->input('apiData'))) {
            throw new NormalStatusException(t('mineadmin.access_denied'), MineCode::NORMAL_STATUS);
        }

        return $this->request->input('apiData');
    }

    /**
     * 获取accessToken.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    #[PostMapping('v1/getAccessToken')]
    public function getAccessToken(): ResponseInterface
    {
        $service = container()->get(SystemAppService::class);
        return $this->success($service->getAccessToken($this->request->all()));
    }

    /**
     * v1 版本.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[RequestMapping('v1/{method}')]
    #[Middlewares([VerifyInterfaceMiddleware::class])]
    public function v1(): ResponseInterface
    {
        $apiData = $this->__init();

        try {
            $class = make($apiData['class_name']);
            // 反射拿参数
            $reflectionMethod = ReflectionManager::reflectMethod($apiData['class_name'], $apiData['method_name']);
            $parameters = $reflectionMethod->getParameters();
            $args = [];
            foreach ($parameters as $parameter) {
                if ($parameter->getType() === null) {
                    continue;
                }
                $className = $parameter->getType()->getName();
                $formRequest = container()->get($className);
                $args[] = $formRequest;
                if ($formRequest instanceof FormRequest) {
                    $this->handleSceneAnnotation($formRequest, $apiData['class_name'], $apiData['method_name'], $parameter->getName());
                    // 验证， 这里逻辑和 验证中间件一样 直接抛异常
                    $formRequest->validateResolved();
                }
            }
            // 反射调用
            return $reflectionMethod->invokeArgs($class, $args);
        } catch (\Throwable $e) {
            if ($e instanceof ValidationException) {
                // 抛出的是验证异常 取一条错误信息返回
                $errors = $e->errors();
                $error = array_shift($errors);
                if (is_array($error)) {
                    $error = array_shift($error);
                }
                throw new NormalStatusException(t('mineadmin.interface_exception') . $error, MineCode::INTERFACE_EXCEPTION);
            }
            if ($e instanceof NoPermissionException) {
                throw new NormalStatusException(t(key: 'mineadmin.api_auth_fail') . $e->getMessage(), code: MineCode::NO_PERMISSION);
            }
            if ($e instanceof TokenException) {
                throw new NormalStatusException(t(key: 'mineadmin.api_auth_exception') . $e->getMessage(), code: MineCode::TOKEN_EXPIRED);
            }

            throw new NormalStatusException(t(key: 'mineadmin.interface_exception') . $e->getMessage(), code: MineCode::INTERFACE_EXCEPTION);
        }
    }

    protected function handleSceneAnnotation(FormRequest $request, string $class, string $method, string $argument): void
    {
        /** @var null|MultipleAnnotation $scene */
        $scene = AnnotationCollector::getClassMethodAnnotation($class, $method)[Scene::class] ?? null;
        if (! $scene) {
            return;
        }

        $annotations = $scene->toAnnotations();
        if (empty($annotations)) {
            return;
        }

        /** @var Scene $annotation */
        foreach ($annotations as $annotation) {
            if ($annotation->argument === null || $annotation->argument === $argument) {
                $request->scene($annotation->scene ?? $method);
                return;
            }
        }
    }
}
