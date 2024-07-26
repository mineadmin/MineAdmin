<?php

namespace App\Kernel\Http;

use Hyperf\Contract\Arrayable;

/**
 * @template T
 */
class Response implements Arrayable
{
    /**
     * @param ResultCode $code
     * @param string|null $message
     * @param T $data
     */
    public function __construct(
        public ResultCode $code = ResultCode::SUCCESS,
        public ?string $message = null,
        public mixed $data = []
    )
    {
        if ($this->message === null) {
            $this->message = ResultCode::getMessage($this->code);
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