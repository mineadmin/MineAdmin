<?php

namespace Mine\Annotation\Api;

use Attribute;
use Hyperf\Di\Annotation\AbstractAnnotation;

#[Attribute(Attribute::TARGET_METHOD|Attribute::IS_REPEATABLE)]
class MApiRequestParam extends AbstractAnnotation
{
    public function __construct(
        // 参数名称
        public string $name,
        // 参数描述
        public string $description,
        // 参数类型  String, Integer, Array, Float, Boolean, Enum, Object, File
        public string $dataType = 'String',
        // 默认值
        public string $defaultValue = '',
        // 是否必须填 1 必填 0 否
        public int $isRequired = 1,
        // 是否启用 1 启动 0 不启用
        public int $status = 1,
    ) {

    }

    public function collectMethod(string $className, ?string $target): void
    {
        MApiRequestParamCollector::collectMethod($className, $target, static::class, $this);
    }

}