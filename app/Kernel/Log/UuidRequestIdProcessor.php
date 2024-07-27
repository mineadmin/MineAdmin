<?php

namespace App\Kernel\Log;

use Monolog\LogRecord;
use Monolog\Processor\ProcessorInterface;
use Ramsey\Uuid\Uuid;
use Hyperf\Context\Context;
use Hyperf\Coroutine\Coroutine;

class UuidRequestIdProcessor implements ProcessorInterface
{
    public const REQUEST_ID = 'log.request.id';

    public static function getUuid(): string
    {
        if (Coroutine::inCoroutine()){
            $requestId = Context::get(static::REQUEST_ID);
            if (is_null($requestId)){
                $requestId = Context::get(static::REQUEST_ID, null, Coroutine::parentId());
                if (!is_null($requestId)){
                    Context::set(static::REQUEST_ID, $requestId);
                }
            }
            if (is_null($requestId)) {
                $requestId = Uuid::uuid4()->toString();
            }
        }else{
            $requestId = Context::set(static::REQUEST_ID,Uuid::uuid4()->toString());
        }
        return $requestId;
    }
    public function __invoke(LogRecord $record)
    {
        $record->extra['request_id'] = static::getUuid();
        return $record;
    }

}