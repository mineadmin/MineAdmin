<?php

namespace App\Http\Common\Scramnble;

use App\Http\Common\ResultCode;
use Dedoc\Scramble\Extensions\ExceptionToResponseExtension;
use Dedoc\Scramble\Support\Generator\Types as OpenApiTypes;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Type;
use Illuminate\Validation\ValidationException;

class ValidationExceptionToResultResponse extends ExceptionToResponseExtension
{
    use ResultExceptionResponse;

    public function shouldHandle(Type $type): bool
    {
        return $type instanceof ObjectType
            && $type->isInstanceOf(ValidationException::class);
    }

    public function toResponse(Type $type)
    {
        $data = (new OpenApiTypes\ObjectType)
            ->setDescription('A detailed description of each field that failed validation.')
            ->additionalProperties((new OpenApiTypes\ArrayType)->setItems(new OpenApiTypes\StringType));

        return $this->resultResponse(
            ResultCode::Unprocessable,
            'Validation result response',
            $data,
        );
    }
}
