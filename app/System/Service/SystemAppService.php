<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\System\Service;

use Api\ApiController;
use App\System\Mapper\SystemAppMapper;
use App\System\Model\SystemApp;
use Hyperf\DbConnection\Db;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * app应用业务
 * Class SystemAppService.
 */
class SystemAppService extends AbstractService
{
    /**
     * @var SystemAppMapper
     */
    public $mapper;

    public function __construct(SystemAppMapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * 生成新的app id.
     * @throws \Exception
     */
    public function getAppId(): string
    {
        return bin2hex(random_bytes(5));
    }

    /**
     * 生成新的app secret.
     * @throws \Exception
     */
    public function getAppSecret(): string
    {
        return base64_encode(bin2hex(random_bytes(32)));
    }

    /**
     * 绑定接口.
     */
    #[Transaction]
    public function bind(int $id, array $ids): bool
    {
        return $this->mapper->bind($id, $ids);
    }

    public function getApiList(?int $id): array
    {
        if (! $id) {
            return [];
        }

        return $this->mapper->getApiList($id);
    }

    /**
     * 获取AccessToken.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws InvalidArgumentException
     */
    public function getAccessToken(array $params): array
    {
        if (empty($params['app_id'])) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_APP_ID_MISSING);
        }

        if (empty($params['signature'])) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_SIGN_MISSING);
        }

        $model = $this->mapper->one(function ($query) use (&$params) {
            $query->where('status', SystemApp::ENABLE)->where('app_id', $params['app_id']);
        });

        if (! $model) {
            throw new NormalStatusException(t('mineadmin.access_denied'), MineCode::API_AUTH_EXCEPTION);
        }

        if ($params['signature'] !== $this->getSignature($model['app_secret'], $params)) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_IDENTITY_ERROR);
        }

        $params['id'] = $model['id'];

        return ['access_token' => app_verify()->getToken($params)];
    }

    /**
     * 获取签名.
     * @param mixed $appSecret
     * @param mixed $params
     */
    public function getSignature($appSecret, $params): string
    {
        unset($params['signature']);

        $data = [
            'sign_ver' => ApiController::SIGN_VERSION,
            'app_secret' => $appSecret,
        ];

        $data = array_merge($data, $params);
        krsort($data);

        return md5(http_build_query($data));
    }

    /**
     * 登录文档.
     */
    public function loginDoc(string $appId, string $appSecret): int
    {
        $model = $this->mapper->one(function ($query) use ($appId, $appSecret) {
            $query->where('app_id', $appId)->where('app_secret', $appSecret);
        }, ['id', 'status']);

        if (! $model) {
            return MineCode::API_PARAMS_ERROR;
        }

        if ($model->status != SystemApp::ENABLE) {
            return MineCode::APP_BAN;
        }

        return MineCode::API_VERIFY_PASS;
    }

    /**
     * 简易验证方式.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function verifyEasyMode(string $appId, string $identity, array &$apiData): int
    {
        $model = $this->mapper->one(function ($query) use ($appId) {
            $query->where('app_id', $appId);
        }, ['id', 'status', 'app_secret']);

        if (! $model) {
            return MineCode::API_PARAMS_ERROR;
        }

        if ($model->status != SystemApp::ENABLE) {
            return MineCode::APP_BAN;
        }

        // 数据库拿的apiData是没有app_id这个字段的，如果有，是注解的，不匹配直接拒绝
        if (isset($apiData['app_id'])) {
            if ($apiData['app_id'] != $appId) {
                return MineCode::API_UNBIND_APP;
            }
        } elseif (! $this->checkAppHasBindApi((int) $model->id, (int) $apiData['id'])) {
            return MineCode::API_UNBIND_APP;
        }

        if ($identity != md5($appId . $model->app_secret)) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_SIGN_ERROR);
        }

        return MineCode::API_VERIFY_PASS;
    }

    /**
     * 正常（复杂）验证方式.
     * @throws InvalidArgumentException
     */
    public function verifyNormalMode(string $accessToken, array &$apiData): int
    {
        $result = app_verify()->check($accessToken);
        if (! $result) {
            return MineCode::API_PARAMS_ERROR;
        }

        $appInfo = app_verify()->getJwt()->getParserData($accessToken);

        // 从数据库拿的apiData是没有app_id这个字段的，如果有，是注解的，不匹配直接拒绝
        if (isset($apiData['app_id'])) {
            if ($apiData['app_id'] != $appInfo['app_id']) {
                return MineCode::API_UNBIND_APP;
            }
        } elseif (! $this->checkAppHasBindApi((int) $appInfo['id'], (int) $apiData['id'])) {
            return MineCode::API_UNBIND_APP;
        }

        return MineCode::API_VERIFY_PASS;
    }

    /**
     * 通过app_id获取app信息和接口数据.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getAppAndInterfaceList(string $appId): array
    {
        return $this->mapper->getAppAndInterfaceList($appId);
    }

    /**
     * 检查app是否绑定某个api接口.
     */
    public function checkAppHasBindApi(int $appId, int $apiId): bool
    {
        return Db::table('system_app_api')
            ->where('app_id', $appId)
            ->where('api_id', $apiId)
            ->count() > 0;
    }
}
