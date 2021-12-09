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

use App\System\Service\SystemApiService;
use App\System\Service\SystemAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Helper\MineCode;
use Mine\MineApi;
use Hyperf\HttpServer\Annotation\Controller;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiDocController
 * @package Api
 * @Controller(prefix="apiDoc")
 */
class ApiDocController extends MineApi
{
    /**
     * @Inject
     * @var SystemAppService
     */
    protected $systemAppService;

    /**
     * @Inject
     * @var SystemApiService
     */
    protected $systemApiService;

    /**
     * 登录文档
     * @PostMapping("login")
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function login(): ResponseInterface
    {
        $app_id = $this->request->input('app_id', '');
        $app_secret = $this->request->input('app_secret', '');

        if (empty($app_id) && empty($app_secret)) {
            return $this->error(t('mineadmin.api_auth_fail'), MineCode::API_PARAMS_ERROR);
        }

        if (($code = $this->systemAppService->verifyEasyMode($app_id, $app_secret)) !== MineCode::API_VERIFY_PASS) {
            return $this->error(t('mineadmin.api_auth_fail'), $code);
        }

        return $this->success();
    }

    /**
     * 通过app id获取接口数据
     * @GetMapping("getAppAndInterfaceList/{id}")
     * @param string $id
     * @return ResponseInterface
     */
    public function getAppAndInterfaceList(string $id): ResponseInterface
    {
        return $this->success($this->systemAppService->getAppAndInterfaceList($id));
    }

    /**
     * @GetMapping("getColumnList/{id}")
     * @param string $id
     * @return ResponseInterface
     */
    public function getColumnList(string $id): ResponseInterface
    {
        return $this->success($this->systemApiService->getColumnListByApiId($id));
    }

}