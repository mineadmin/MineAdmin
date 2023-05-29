<?php
// 自定义函数库

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