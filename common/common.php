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
use App\Setting\Service\SettingConfigService;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

// 自定义函数库

if (! function_exists('env')) {
    /**
     * 获取环境变量信息.
     */
    function env(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Support\env($key, $default);
    }
}

if (! function_exists('config')) {
    /**
     * 获取配置信息.
     */
    function config(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Config\config($key, $default);
    }
}

if (! function_exists('make')) {
    /**
     * Create an object instance, if the DI container exist in ApplicationContext,
     * then the object will be created by DI container via `make()` method, if not,
     * the object will create by `new` keyword.
     */
    function make(string $name, array $parameters = [])
    {
        return \Hyperf\Support\make($name, $parameters);
    }
}

if (! function_exists('sys_config')) {
    /**
     * 获取后台系统配置.
     *
     * @throws RedisException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function sys_config(string $key, mixed $default = null): mixed
    {
        return container()->get(SettingConfigService::class)->getConfigByKey($key) ?? $default;
    }
}

if (! function_exists('sys_group_config')) {
    /**
     * 获取后台系统配置.
     *
     * @param null|mixed $default
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function sys_group_config(string $groupKey, mixed $default = []): mixed
    {
        return container()->get(SettingConfigService::class)->getConfigByGroupKey($groupKey) ?? $default;
    }
}
