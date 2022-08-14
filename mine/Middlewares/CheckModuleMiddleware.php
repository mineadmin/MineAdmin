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
namespace Mine\Middlewares;

use App\Setting\Service\ModuleService;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\Di\Annotation\Inject;
use Mine\Helper\Str;
use Mine\Exception\NormalStatusException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * 检查模块
 */
class CheckModuleMiddleware implements MiddlewareInterface
{
    /**
     * 模块服务
     * @var ModuleService
     */
    #[Inject]
    protected ModuleService $service;

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $uri = $request->getUri();

        if ($uri->getPath() !== '/favicon.ico' && mb_substr_count($uri->getPath(), '/') > 1) {

            list($empty, $moduleName, $controllerName) = explode('/', $uri->getPath());

            $path = $moduleName . '/' . $controllerName;

            $moduleName = Str::lower($moduleName);

            $module['enabled'] = false;

            foreach ($this->service->getModuleCache() as $name => $item) if (Str::lower($name) === $moduleName) {
                $module = $item;
                break;
            }

            $annotation = AnnotationCollector::getClassesByAnnotation('Hyperf\HttpServer\Annotation\Controller');

            foreach ($annotation as $item) if ( $item->server === 'http' && $item->prefix === $path && !$module['enabled']) {
                throw new NormalStatusException('模块被禁用', 500);
            }
        }

        return $handler->handle($request);
    }
}