<?php

namespace App\Http\Common;

use Hyperf\Contract\Arrayable;
use Hyperf\Swagger\Annotation\Property;
use OpenApi\Attributes\Schema;

/**
 * @template T
 */
#[Schema(title: 'Api Response',description: 'Api Response')]
class Result implements Arrayable
{
    /**
     * @param ResultCode $code
     * @param string|null $message
     * @param T $data
     */
    public function __construct(
        #[Property(ref: 'ResultCode', title: '响应码')]
        public ResultCode $code = ResultCode::SUCCESS,
        #[Property(title: '响应消息',type: 'string')]
        public ?string $message = null,
        #[Property(title: '响应数据',type: 'object',nullable: true)]
        public mixed $data = []
    )
    {
        if ($this->message === null) {
            $this->message = ResultCode::getMessage($this->code->value);
        }
    }

    public function toArray(): array
    {
        return [
            'code' => $this->code->value,
            'message' => $this->message,
            'data' => $this->data,
        ];
    }
}