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

namespace Mine\Casbin;

use Casbin\Enforcer;
use Casbin\Model\Model;
use Casbin\Persist\Adapter;
use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

final class Factory
{
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ContainerInterface $container
    ) {}

    public function __invoke()
    {
        return $this->enforcer();
    }

    public function enforcer()
    {
        return new Enforcer(
            $this->getModel(),
            $this->getAdapter(),
            (bool) $this->config->get('permission.log.enabled', false)
        );
    }

    private function getModel(): Model
    {
        $model = new Model();
        if ($this->config->get('permission.model.type') === 'file') {
            $model->loadModel($this->config->get('permission.model.path'));
        }

        if ($this->config->get('permission.model.type') === 'test') {
            $model->loadModelFromText($this->config->get('permission.model.text'));
        }
        return $model;
    }

    private function getAdapter(): Adapter
    {
        return $this->container->get($this->config->get('permission.adapter'));
    }
}
