<?php

declare(strict_types=1);
namespace App\Setting\Controller\Settings;

use App\Setting\Request\Setting\SettingConfigCreateRequest;
use App\Setting\Service\SettingConfigService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\DeleteMapping;
use Hyperf\HttpServer\Annotation\GetMapping;
use Hyperf\HttpServer\Annotation\PostMapping;
use Mine\Annotation\Auth;
use Mine\Annotation\DeleteCache;
use Mine\Annotation\OperationLog;
use Mine\Annotation\Permission;
use Mine\MineController;

/**
 * 系统配置控制器
 * Class SystemConfigController
 * @package App\Setting\Controller\Settings
 */
#[Controller(prefix: "setting/config")]
class SystemConfigController extends MineController
{
    #[Inject]
    protected SettingConfigService $service;

    /**
     * 获取系统组配置 （不验证身份和权限）
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getSysConfig")]
    public function getSysConfig(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getSystemGroupConfig());
    }

    /**
     * 获取系统组配置
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getSystemConfig"), Permission("setting:config:getSystemConfig")]
    public function getSystemGroupConfig(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getSystemGroupConfig());
    }

    /**
     * 获取扩展组配置
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getExtendConfig"), Permission("setting:config:getExtendConfig")]
    public function getExtendGroupConfig(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getExtendGroupConfig());
    }

    /**
     * 按组名获取配置
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[GetMapping("getConfigByGroup"), Auth]
    public function getConfigByGroup(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getConfigByGroup($this->request->input('groupName', '')));
    }

    /**
     * 按key获取配置
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("getConfigByKey")]
    public function getConfigByKey(): \Psr\Http\Message\ResponseInterface
    {
        return $this->success($this->service->getConfigByKey($this->request->input('key', '')));
    }

    /**
     * 保存系统配置组数据
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("saveSystemConfig"), Permission("setting:config:saveSystemConfig"), OperationLog]
    public function saveSystemConfig(): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->saveSystemConfig($this->request->all()) ? $this->success() : $this->error();
    }

    /**
     * 保存配置
     * @param SettingConfigCreateRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("save"), Permission("setting:config:save"), OperationLog]
    public function save(SettingConfigCreateRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->save($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 更新配置
     * @param SettingConfigCreateRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[PostMapping("update"), Permission("setting:config:update"), OperationLog]
    public function update(SettingConfigCreateRequest $request): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->updated($request->validated()) ? $this->success() : $this->error();
    }

    /**
     * 删除配置
     * @param string $key
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    #[DeleteMapping("delete/{key}"), Permission("setting:config:delete"), OperationLog]
    public function delete(string $key): \Psr\Http\Message\ResponseInterface
    {
        return $this->service->delete($key) ? $this->success() : $this->error();
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