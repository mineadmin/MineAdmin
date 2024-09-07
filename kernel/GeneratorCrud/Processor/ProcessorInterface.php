<?php

namespace Mine\Kernel\GeneratorCrud\Processor;

use Mine\Kernel\GeneratorCrud\Context;
use Mine\Kernel\GeneratorCrud\Entity\GeneratorEntity;

interface ProcessorInterface
{
    public function process(Context $c): GeneratorEntity;
}