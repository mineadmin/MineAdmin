<?php

namespace App\Http\Common\Scramnble;

use App\Http\Common\ResultCode;
use Dedoc\Scramble\Support\Generator\Response;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types as OpenApiTypes;

trait ResultExceptionResponse
{
    protected function resultResponse(ResultCode $code, string $description, OpenApiTypes\Type $data): Response
    {
        $schema = (new OpenApiTypes\ObjectType)
            ->addProperty(
                'code',
                (new OpenApiTypes\NumberType('integer'))
                    ->setDescription('系统响应码')
                    ->enum([$code->value])
            )
            ->addProperty(
                'message',
                (new OpenApiTypes\StringType)
                    ->setDescription('系统消息')
            )
            ->addProperty('data', $data)
            ->setRequired(['code', 'message', 'data']);

        return Response::make(200)
            ->setDescription($description)
            ->setContent('application/json', Schema::fromType($schema));
    }
}
