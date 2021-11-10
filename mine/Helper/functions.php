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

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Utils\ApplicationContext;
use Mine\Helper\LoginUser;
use Psr\Log\LoggerInterface;

if (! function_exists('container')) {

    /**
     * 获取容器实例
     * @return \Psr\Container\ContainerInterface
     */
    function container(): \Psr\Container\ContainerInterface
    {
        return ApplicationContext::getContainer();
    }

}

if (! function_exists('redis')) {

    /**
     * 获取Redis实例
     * @return \Hyperf\Redis\Redis
     */
    function redis(): \Hyperf\Redis\Redis
    {
        return container()->get(\Hyperf\Redis\Redis::class);
    }

}

if (! function_exists('console')) {

    /**
     * 获取控制台输出实例
     * @return StdoutLoggerInterface
     */
    function console(): StdoutLoggerInterface
    {
        return container()->get(StdoutLoggerInterface::class);
    }

}

if (! function_exists('logger')) {

    /**
     * 获取日志实例
     * @param string $name
     * @return LoggerInterface
     */
    function logger(string $name = 'Log'): LoggerInterface
    {
        return container()->get(LoggerFactory::class)->get($name);
    }

}


if (! function_exists('user')) {
    /**
     * 获取当前登录用户实例
     * @return LoginUser
     */
    function user(): LoginUser
    {
        return new LoginUser();
    }
}

if (! function_exists('format_size')) {
    /**
     * 格式化大小
     * @param int $size
     * @return string
     */
    function format_size(int $size): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $index = 0;
        for ($i = 0; $size >= 1024 && $i < 5; $i++) {
            $size /= 1024;
            $index = $i;
        }
        return round($size, 2) . $units[$index];
    }
}

if (! function_exists('t')) {
    /**
     * 多语言函数
     * @param string $key
     * @param array $replace
     * @return string
     */
    function t(string $key, array $replace = []): string
    {
        $language = explode(
            ',',
            container()->get(\Mine\MineRequest::class)->getHeaderLine('accept-language')
        )[0] ?? 'zh_CN';
        return __($key, $replace, $language);
    }
}

