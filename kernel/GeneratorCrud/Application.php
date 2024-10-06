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

use Hyperf\Collection\Collection;
use Mine\GeneratorCrud\Entity\TableEntity;
use Mine\GeneratorCrud\Event\GeneratorStaringEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final class Application implements GeneratorInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly EventDispatcherInterface $dispatcher
    ) {}

    public function generate(TableEntity $table, array $config = [], array $extra = []): array
    {
        $context = new Context(Collection::make($config), Collection::empty(), Collection::make($extra), $table, Collection::empty());
        $this->dispatch(new GeneratorStaringEvent($context));
        foreach ($this->config->getProcessors() as $processor) {
            $context->getProcessors()->push($processor);
            $context->getEntities()->push($processor->process($context));
        }
        return $context->getEntities()->all();
    }

    private function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
