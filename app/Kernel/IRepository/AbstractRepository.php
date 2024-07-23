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

namespace App\Kernel\IRepository;

use Hyperf\Context\Context;
use Mine\MineModel;

/**
 * Class AbstractRepository.
 */
abstract class AbstractRepository
{
    use RepositoryTrait;

    /**
     * @var class-string|MineModel
     */
    public $model;

    public function __construct()
    {
        $this->assignModel();
    }

    /**
     * 魔术方法，从类属性里获取数据.
     * @return mixed|string
     */
    public function __get(string $name)
    {
        return $this->getAttributes()[$name] ?? '';
    }

    abstract public function assignModel();

    /**
     * 把数据设置为类属性.
     */
    public static function setAttributes(array $data)
    {
        Context::set('attributes', $data);
    }

    /**
     * 获取数据.
     */
    public function getAttributes(): array
    {
        return Context::get('attributes', []);
    }
}
