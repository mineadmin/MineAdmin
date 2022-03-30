<?php

declare(strict_types=1);
namespace App\System\Service;

use Api\ApiController;
use App\System\Mapper\SystemAppMapper;
use App\System\Model\SystemApp;
use Mine\Abstracts\AbstractService;
use Mine\Annotation\Transaction;
use Mine\Exception\NormalStatusException;
use Mine\Helper\MineCode;

/**
 * app应用业务
 * Class SystemAppService
 * @package App\System\Service
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
     * 生成新的app id
     * @return string
     * @throws \Exception
     */
    public function getAppId(): string
    {
        return bin2hex(random_bytes(5));
    }

    /**
     * 生成新的app secret
     * @return string
     * @throws \Exception
     */
    public function getAppSecret(): string
    {
        return base64_encode(bin2hex(random_bytes(32)));
    }

    /**
     * 绑定接口
     * @param int $id
     * @param array $ids
     * @return bool
     * @Transaction
     */
    public function bind(int $id, array $ids): bool
    {
        return $this->mapper->bind($id, $ids);
    }

    /**
     * @param int|null $id
     * @return array
     */
    public function getApiList(?int $id): array
    {
        if (! $id) return [];

        return $this->mapper->getApiList($id);
    }

    /**
     * 获取AccessToken
     * @param array $params
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getAccessToken(array $params): array
    {
        if (empty($params['app_id'])) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_APP_ID_MISSING);
        }

        if (empty($params['signature'])) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_SIGN_MISSING);
        }

        $model = $this->mapper->one(function($query) use(&$params){
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
     * 获取签名
     * @param $appSecret
     * @param $params
     * @return string
     */
    public function getSignature($appSecret, $params): string
    {
        unset($params['signature']);

        $data = [
            'sign_ver'   => ApiController::SIGN_VERSION,
            'app_secret' => $appSecret
        ];

        $data = array_merge($data, $params);
        krsort($data);

        return md5(http_build_query($data));
    }

    /**
     * 简易验证方式
     * @param string $appId
     * @param string $identity
     * @return int
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function verifyEasyMode(string $appId, string $identity): int
    {
        $model = $this->mapper->one(function($query) use($appId){
            $query->where('app_id', $appId);
        }, ['id', 'status', 'app_secret']);

        if (! $model) {
            return MineCode::API_PARAMS_ERROR;
        }

        if ($model->status != SystemApp::ENABLE) {
            return MineCode::APP_BAN;
        }

        if ($identity != md5($model->app_id . $model->app_secret)) {
            throw new NormalStatusException(t('mineadmin.api_auth_fail'), MineCode::API_SIGN_ERROR);
        }

        return MineCode::API_VERIFY_PASS;
    }

    /**
     * 正常（复杂）验证方式
     * @param string $accessToken
     * @return int
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function verifyNormalMode(string $accessToken): int
    {
        return app_verify()->check($accessToken) ? MineCode::API_VERIFY_PASS : MineCode::API_PARAMS_ERROR;
    }

    /**
     * 通过app_id获取app信息和接口数据
     * @param string $appId
     * @return array
     */
    public function getAppAndInterfaceList(string $appId): array
    {
        return $this->mapper->getAppAndInterfaceList($appId);
    }
}