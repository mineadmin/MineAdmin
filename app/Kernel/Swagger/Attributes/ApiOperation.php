<?php

namespace App\Kernel\Swagger\Attributes;

use Attribute;
use OpenApi\Annotations\Operation;
use OpenApi\Attributes\OperationTrait;

#[Attribute(Attribute::TARGET_METHOD)]
class ApiOperation extends Operation
{
    use OperationTrait;
}