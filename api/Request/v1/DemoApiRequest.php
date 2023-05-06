<?php

namespace Api\Request\v1;

use Mine\MineFormRequest;

class DemoApiRequest extends MineFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * 用户信息验证规则  这里是api访问名，不是方法名，
     * 如果感觉乱，可以使用继承标准formRequest
     * return array
     */
    public function getUserInfoRules(): array
    {
        return [
            'id' => 'required|numeric',
        ];
    }

    /**
     * 验证消息.
     */
    public function messages(): array
    {
        return [
            'id.required' => 'id参数缺失',
            'id.numeric' => 'id必须是数字',
        ];
    }
}