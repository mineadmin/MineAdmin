<?php

namespace App\Kernel\Swagger\Processor;

use App\Kernel\Swagger\Processor\AbstractProcessor;
use OpenApi\Analysis;

class FormRequestProcessor extends AbstractProcessor
{

    function handle(Analysis $analysis)
    {
        return $analysis;
    }
}