<?php
// 自定义函数库

use App\System\Vo\QueueMessageVo;
use Mine\Interfaces\ServiceInterface\QueueLogServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

if (! function_exists('env')) {

    /**
     * 获取环境变量信息
     *
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function env(string $key, mixed $default = null): mixed
    {
        return \Hyperf\Support\env($key, $default);
    }

}

if (! function_exists('config')) {

    /**
     * 获取配置信息
     *
     * @param string $key
     * @param null|mixed $default
     * @return mixed
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
     * 获取后台系统配置
     *
     * @param string $key
     * @param null|mixed $default
     * @return mixed
     * @throws RedisException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function sys_config(string $key, mixed $default = null): mixed
    {
        return container()->get(\App\Setting\Service\SettingConfigService::class)->getConfigByKey($key) ?? $default;
    }

}

if (! function_exists('sys_group_config')) {

    /**
     * 获取后台系统配置
     *
     * @param string $groupKey
     * @param null|mixed $default
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function sys_group_config(string $groupKey, mixed $default = []): mixed
    {
        return container()->get(\App\Setting\Service\SettingConfigService::class)->getConfigByGroupKey($groupKey) ?? $default;
    }
}

if (! function_exists('push_queue_message')) {
    /**
     * 推送消息到队列.
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function push_queue_message(QueueMessageVo $message, array $receiveUsers = []): bool
    {
        return container()
            ->get(QueueLogServiceInterface::class)
            ->pushMessage($message, $receiveUsers);
    }
}

if (! function_exists('add_queue')) {
    /**
     * 添加任务到队列.
     * @throws Throwable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function add_queue(App\System\Vo\AmqpQueueVo $amqpQueueVo): bool
    {
        return container()
            ->get(QueueLogServiceInterface::class)
            ->addQueue($amqpQueueVo);
    }
}

if (!function_exists('delete_cache')){
    /**
     * 删除框架自带的注解缓存
     * @param string $prefix
     * @param array $args
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    function delete_cache(string $prefix,array $args): void
    {
        \Hyperf\Context\ApplicationContext::getContainer()
            ->get(\Psr\EventDispatcher\EventDispatcherInterface::class)
            ->dispatch(new \Hyperf\Cache\Listener\DeleteListenerEvent(
                $prefix,$args
            ));
    }
}