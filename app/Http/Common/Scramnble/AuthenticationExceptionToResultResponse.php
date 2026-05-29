<?php

namespace App\Http\Common\Scramnble;

use App\Http\Common\ResultCode;
use Dedoc\Scramble\Extensions\ExceptionToResponseExtension;
use Dedoc\Scramble\Support\Generator\Types as OpenApiTypes;
use Dedoc\Scramble\Support\Type\ObjectType;
use Dedoc\Scramble\Support\Type\Type;
use Illuminate\Auth\AuthenticationException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationExceptionToResultResponse extends ExceptionToResponseExtension
{
    use ResultExceptionResponse;

    public function shouldHandle(Type $type): bool
    {
        return $type instanceof ObjectType
            && ($type->isInstanceOf(AuthenticationException::class)
                || $type->isInstanceOf(JWTException::class)
                || $type->isInstanceOf(UnauthorizedHttpException::class));
    }

    public function toResponse(Type $type)
    {
        return $this->resultResponse(
            ResultCode::Unauthorized,
            'Unauthenticated result response',
            new OpenApiTypes\ArrayType,
        );
    }
}
