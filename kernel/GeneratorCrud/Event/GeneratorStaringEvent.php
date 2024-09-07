<?php

namespace Mine\Kernel\GeneratorCrud\Event;

use Mine\Kernel\GeneratorCrud\Context;

class GeneratorStaringEvent
{
    public function __construct(
        public Context $context
    ){}
}