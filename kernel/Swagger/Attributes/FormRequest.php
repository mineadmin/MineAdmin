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

namespace Mine\Swagger\Attributes;

use Hyperf\Collection\Arr;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[\Attribute(\Attribute::TARGET_CLASS)]
class FormRequest extends Schema
{
    public function __construct(
        ?string $schema = null,
        ?string $title = null,
        ?string $description = null,
        ?array $required = null,
        ?array $properties = null,
        array $only = []
    ) {
        $properties = $this->parserProperties($properties, $schema);
        if ($only) {
            $properties = Arr::where($properties, static function (Property $item) use ($only) {
                return \in_array($item->property, $only, true);
            });
        }
        parent::__construct(
            title: $title,
            description: $description,
            required: $required,
            properties: $properties
        );
    }

    protected function parserSchemaProperties(\ReflectionClass $reflectionClass, string $schema): array
    {
        $result = [];
        foreach ($reflectionClass->getProperties() as $reflectionProperty) {
            $propertyList = $reflectionProperty->getAttributes(Property::class);
            if ($propertyList) {
                $result[] = $propertyList[0]->newInstance();
            }
        }
        return $result;
    }

    private function parserProperties(?array $properties, string $schema): array
    {
        $result = [];
        if (empty($properties)) {
            $reflectionClass = new \ReflectionClass($schema);
            return $this->parserSchemaProperties($reflectionClass, $schema);
        }
        return $result;
    }
}
