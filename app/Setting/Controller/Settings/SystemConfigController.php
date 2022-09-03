<?php

declare(strict_types=1);
namespace App\Setting\Controller\Settings;

use App\Setting\Request\SettingConfigRequest;
use App\Setting\Service\SettingConfigService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * 系统配置控制器
 * Class SystemConfigController
 * @package App\Setting\Controller\Settings
 */
#[Controller(prefix: "setting/config"), Auth]
class SystemConfigController extends MineController
{
    #[Inject]
    protected SettingConfigService $service;

    /**
     * 获取配置列表
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("index"), Permission("setting:config, setting:config:index")]
    public function index(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getList($this->request->all()));
    }

    /**
     * 保存配置
     * @param SettingConfigRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("setting:config:save"), OperationLog]
    public function save(SettingConfigRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->save($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 更新配置
     * @param SettingConfigRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("update"), Permission("setting:config:update"), OperationLog]
    public function update(SettingConfigRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->updated($this->request->input('key'), $request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 按 keys 更新配置
     * @param SettingConfigRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("updateByKeys"), Permission("setting:config:update"), OperationLog]
    public function updateByKeys(): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->updatedByKeys($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 删除配置
     * @param string $key
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("delete"), Permission("setting:config:delete"), OperationLog]
    public function delete(): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->delete((array) $this->request->input('ids', [])) ? $this->success() : $this->error();
    }

    /**
     * 清除配置缓存
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("clearCache"), Permission("setting:config:clearCache"), OperationLog]
    public function clearCache(): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->clearCache() ? $this->success() : $this->error();
    }
}