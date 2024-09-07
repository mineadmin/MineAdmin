<?php

namespace Mine\Kernel\GeneratorCrud;

use Hyperf\Collection\Collection;
use Mine\Kernel\GeneratorCrud\Entity\TableEntity;
use Mine\Kernel\GeneratorCrud\Event\GeneratorStaringEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

final class Application implements GeneratorInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly EventDispatcherInterface $dispatcher
    ){}

    private function dispatch(object $event): void
    {
        $this->dispatcher->dispatch($event);
    }

    public function generate(TableEntity $table,array $config = [],array $extra = []): array
    {
        $context = new Context(Collection::make($config),Collection::empty(),Collection::make($extra),$table,Collection::empty());
        $this->dispatch(new GeneratorStaringEvent($context));
        foreach ($this->config->getProcessors() as $processor){
            $context->getProcessors()->push($processor);
            $context->getEntities()->push($processor->process($context));
        }
        return $context->getEntities()->all();
    }

}