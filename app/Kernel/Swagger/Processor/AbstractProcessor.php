<?php

namespace App\Kernel\Swagger\Processor;

use OpenApi\Analysis;
use OpenApi\Processors\ProcessorInterface;

abstract class AbstractProcessor implements ProcessorInterface
{
    public function __invoke(Analysis $analysis)
    {
        $this->handle($analysis);
        return $analysis;
    }

    abstract function handle(Analysis $analysis);
}