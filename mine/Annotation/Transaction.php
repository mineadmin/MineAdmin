<?php

namespace Mine\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;

/**
 * 数据库事务注解。
 * @Annotation
 * @Target({"METHOD"})
 */
class Transaction extends AbstractAnnotation {}