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

declare(strict_types = 1);
namespace Mine\Abstracts;

use Mine\Traits\ServiceTrait;
use Hyperf\Context\Context;

abstract class AbstractService
{
    use ServiceTrait;

    public $mapper;

    /**
     * 把数据设置为类属性
     * @param array $data
     */
    public function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 魔术方法，从类属性里获取数据
     * @param string $name
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    /**
     * 获取数据
     * @return array
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
