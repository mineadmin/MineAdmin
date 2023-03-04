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
namespace Api\Middleware;

use App\System\Service\SystemAppService;
use Mine\Event\ApiAfter;
use Mine\Event\ApiBefore;
use App\System\Model\SystemApi;
use App\System\Service\SystemApiService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Context\Context;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Mine\MineRequest;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class VerifyInterfaceMiddleware implements MiddlewareInterface
{
    /**
     * 事件调度器
     * @var EventDispatcherInterface
     */
    #[Inject]
    protected EventDispatcherInterface $evDispatcher;

    /**
     * 验证检查接口
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
        return $this->run($request, $handler);
    }

    /**
     * 访问接口鉴权处理
     * @param ServerRequestInterface $request
     * @return int
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function auth(ServerRequestInterface $request): int
    {
        try {
            /* @var $service SystemAppService */
            $service = container()->get(SystemAppService::class);
            $queryParams = $request->getQueryParams();
            switch ($this->_getApiData()['auth_mode']) {
                case SystemApi::AUTH_MODE_EASY:
                    if (empty($queryParams['app_id'])) {
                        return MineCode::API_APP_ID_MISSING;
                    }
                    if (empty($queryParams['identity'])) {
                        return MineCode::API_IDENTITY_MISSING;
                    }
                    return $service->verifyEasyMode($queryParams['app_id'], $queryParams['identity'], $this->_getApiData());
                case SystemApi::AUTH_MODE_NORMAL:

                    if (empty($queryParams['access_token'])) {
                        return MineCode::API_ACCESS_TOKEN_MISSING;
                    }
                    return $service->verifyNormalMode($queryParams['access_token'], $this->_getApiData());
                default:
                    throw new \RuntimeException();
            }
        } catch (\Throwable $e) {
            throw new NormalStatusException(t('mineadmin.api_auth_exception'), MineCode::API_AUTH_EXCEPTION);
        }
    }

    /**
     * API常规检查
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected function apiModelCheck($request): ServerRequestInterface
    {
        $service = container()->get(SystemApiService::class);
        $apiModel = $service->mapper->one(function($query) {
            $request = container()->get(MineRequest::class);
            $query->where('access_name', $request->route('method'));
        });

        // 检查接口是否存在
        if (! $apiModel) {
            throw new NormalStatusException(t('mineadmin.not_found'), MineCode::NOT_FOUND);
        }

        // 检查接口是否停用
        if ($apiModel['status'] == SystemApi::DISABLE) {
            throw new NormalStatusException(t('mineadmin.api_stop'), MineCode::RESOURCE_STOP);
        }

        // 检查接口请求方法
        if ($apiModel['request_mode'] !== SystemApi::METHOD_ALL && $request->getMethod()[0] !== $apiModel['request_mode']) {
            throw new NormalStatusException(
                t('mineadmin.not_allow_method', ['method' => $request->getMethod()]),
                MineCode::METHOD_NOT_ALLOW
            );
        }

        $this->_setApiData($apiModel->toArray());

        // 合并入参
        return $request->withParsedBody(array_merge(
            $request->getParsedBody(), ['apiData' => $apiModel->toArray()]
        ));
    }

    /**
     * 运行
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    protected function run(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->evDispatcher->dispatch(new ApiBefore());

        $request = $this->apiModelCheck($request);

        if (($code = $this->auth($request)) !== MineCode::API_VERIFY_PASS) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), $code);
        }

        $result = $handler->handle($request);

        $event = new ApiAfter($this->_getApiData(), $result);
        $this->evDispatcher->dispatch($event);

        return $event->getResult();
    }

    /**
     * 设置协程上下文
     * @param array $data
     */
    private function _setApiData(array $data)
    {
        Context::set('apiData', $data);
    }

    /**
     * 获取协程上下文
     * @return array
     */
    private function _getApiData(): array
    {
        return Context::get('apiData', []);
    }
}