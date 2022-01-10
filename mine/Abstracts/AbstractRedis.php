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

declare (strict_types = 1);
namespace Mine\Abstracts;

use Hyperf\Config\Annotation\Value;

/**
 * Class AbstractRedis
 * @package Mine\Abstracts
 */
abstract class AbstractRedis
{
    /**
     * @Value("cache.default.prefix")
     * @var string
     */
    protected $prefix;

    /**
     * key 类型名
     * @var string
     */
    protected $typeName;

    /**
     * 获取实例
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function getInstance()
    {
        return container()->get(static::class);
    }

    /**
     * 获取redis实例
     * @return \Hyperf\Redis\Redis
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function redis(): \Hyperf\Redis\Redis
    {
        return redis();
    }

    /**
     * 获取key
     * @param string $key
     * @return string
     */
    public function getKey(string $key): ?string
    {
        return empty($key) ? null : ($this->prefix . trim($this->typeName, ':') . ':' . $key);
    }

}