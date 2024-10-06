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

namespace Mine\GeneratorCrud;

use Hyperf\Contract\ConfigInterface;
use Mine\GeneratorCrud\Processor\ProcessorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class Config
{
    public function __construct(
        private readonly ConfigInterface $config,
        private readonly ContainerInterface $container
    ) {}

    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    public function isEnable(): bool
    {
        return (bool) $this->config->get('generator.enable', false);
    }

    /**
     * @return iterable<ProcessorInterface>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getProcessors(): iterable
    {
        foreach ($this->config->get('generator.processors', []) as $processor) {
            yield $this->container->get($processor);
        }
    }
}
