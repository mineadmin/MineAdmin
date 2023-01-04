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
namespace Api;

use App\System\Service\SystemAppService;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Mine\MineApi;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;
use Api\Middleware\VerifyInterfaceMiddleware;

/**
 * Class ApiController
 * @package Api
 */
#[Controller(prefix: "api")]
class ApiController extends MineApi
{
    public const SIGN_VERSION = '1.0';

    /**
     * 获取accessToken
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[PostMapping("v1/getAccessToken")]
    public function getAccessToken(): ResponseInterface
    {
        $service = container()->get(SystemAppService::class);
        return $this->success($service->getAccessToken($this->request->all()));
    }

    /**
     * v1 版本
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[RequestMapping("v1/{method}")]
    #[Middlewares([ VerifyInterfaceMiddleware::class ])]
    public function v1(): ResponseInterface
    {
        $apiData = $this->__init();

        try {
            $class = make($apiData['class_name']);
            return $class->{$apiData['method_name']}();
        } catch (\Throwable $e) {
            throw new NormalStatusException(
                t('mineadmin.interface_exception') . $e->getMessage(),
                MineCode::INTERFACE_EXCEPTION
            );
        }
    }

    /**
     * 初始化
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function __init()
    {
        if (empty($this->request->input('apiData'))) {
            throw new NormalStatusException(t('mineadmin.access_denied'), MineCode::NORMAL_STATUS);
        }

        return $this->request->input('apiData');
    }
}