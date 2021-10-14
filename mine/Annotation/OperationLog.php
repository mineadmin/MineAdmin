<?php

namespace Mine\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 记录操作日志注解。
 * @Annotation
 * @Target({"METHOD"})
 */
class OperationLog extends AbstractAnnotation {}