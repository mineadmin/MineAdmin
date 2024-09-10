<?php

namespace Mine\Kernel\GeneratorCrud\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\BootApplication;

class RegisterViewPathListener implements ListenerInterface
{
    public function __construct(

    ){}

    public function listen(): array
    {
        return [
            BootApplication::class
        ];
    }

    public function process(object $event): void
    {

    }

}