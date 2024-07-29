<?php

declare(strict_types=1);
/**
 * This file is part of MineAdmin.
 *
 * @link     https://www.mineadmin.com
 * @document https://doc.mineadmin.com
 * @contact  root@imoi.cn
 * @license  https://github.com/mineadmin/MineAdmin/blob/master/LICENSE
 */

namespace App\Kernel\Swagger\Attributes;

use Hyperf\Swagger\Annotation\JsonContent;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Response as Base;
use OpenApi\Generator;

#[\Attribute(\Attribute::IS_REPEATABLE | \Attribute::TARGET_METHOD)]
class ResultResponse extends Base
{
    public function __construct(
        object $instance,
        ?string $title = null,
        ?array $examples = null,
        ?string $description = null,
        mixed $example = Generator::UNDEFINED,
        ?array $headers = null,
        ?int $response = 200,
    ) {
        parent::__construct(
            response: $response,
            description: $description,
            headers: $headers,
            content: $this->generatorResponse($instance, $title, $example, $examples),
        );
    }

    private function generatorResponse(object $instance, ?string $title, mixed $example, ?array $examples = []): JsonContent
    {
        $properties = $this->parserInstance($instance);
        return new JsonContent(examples: $examples, title: $title, properties: $properties, example: $example);
    }

    private function parserInstance(object $instance): array
    {
        $result = [];
        $reflectionClass = new \ReflectionClass($instance);
        foreach ($reflectionClass->getProperties() as $property) {
            $result[] = $this->parserProperty($property, $instance->{$property->getName()});
        }
        return $result;
    }

    private function parserProperty(\ReflectionProperty $reflectionProperty, mixed $value): Property
    {
        $property = new Property();
        $property->property = $reflectionProperty->getName();
        $typeName = $reflectionProperty->getType()?->getName();
        if ($property->ref === Generator::UNDEFINED && class_exists($typeName)) {
            $property->ref = $typeName;
        }
        if ($property->ref === Generator::UNDEFINED && is_object($value)) {
            $property->ref = get_class($value);
        }
        if ($property->ref === Generator::UNDEFINED && is_string($value) && class_exists($value)) {
            $property->ref = $value;
        }

        if ($property->ref === Generator::UNDEFINED) {
            $property->type = $typeName;
        }
        return $property;
    }
}
