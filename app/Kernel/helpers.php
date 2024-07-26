<?php
namespace App\Kernel;

use Hyperf\Context\ApplicationContext;
use Hyperf\Contract\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

function container(): ContainerInterface
{
    return ApplicationContext::getContainer();
}

function app(): ContainerInterface
{
    return container();
}

function event(mixed $event): void
{
    container()->get(EventDispatcherInterface::class)->dispatch($event);
}



