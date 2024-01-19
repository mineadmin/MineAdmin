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

use App\System\Service\SystemApiService;
use App\System\Service\SystemAppService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Api\MApiCollector;
use Mine\Helper\MineCode;
use Mine\MineApi;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ApiDocController.
 */
#[Controller(prefix: 'apiDoc')]
class ApiDocController extends MineApi
{
    #[Inject]
    protected SystemAppService $systemAppService;

    #[Inject]
    protected SystemApiService $systemApiService;

    /**
     * 登录文档.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[PostMapping('login')]
    public function login(): ResponseInterface
    {
        $app_id = $this->request->input('app_id', '');
        $app_secret = $this->request->input('app_secret', '');

        if (empty($app_id) && empty($app_secret)) {
            return $this->error(t('mineadmin.api_auth_fail'), MineCode::API_PARAMS_ERROR);
        }

        if (($code = $this->systemAppService->loginDoc($app_id, $app_secret)) !== MineCode::API_VERIFY_PASS) {
            return $this->error(t('mineadmin.api_auth_fail'), $code);
        }

        return $this->success();
    }

    /**
     * 通过app id获取接口数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getAppAndInterfaceList/{id}')]
    public function getAppAndInterfaceList(string $id): ResponseInterface
    {
        $appAndInterfaceList = $this->systemAppService->getAppAndInterfaceList($id);

        $apis = MApiCollector::getApiInfosByAppId($id);

        // 注解与数据库定义的合并
        $apis = array_merge($appAndInterfaceList['apis'], $apis);
        $appAndInterfaceList['apis'] = $apis;

        return $this->success($appAndInterfaceList);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[GetMapping('getColumnList/{id}')]
    public function getColumnList(string $id): ResponseInterface
    {
        // 如果api注解收集器里有，直接返回信息
        if (MApiCollector::getApiInfo($id)) {
            return $this->success(MApiCollector::getApiInfo($id));
        }

        return $this->success($this->systemApiService->getColumnListByApiId($id));
    }
}
