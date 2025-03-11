<?php

namespace App\Library\DataPermission\Attribute;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_CLASS|Attribute::TARGET_METHOD)]
final class DataScope extends AbstractAnnotation
{

}