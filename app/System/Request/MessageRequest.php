<?php
declare(strict_types=1);
namespace App\System\Request;

use Mine\MineFormRequest;

class MessageRequest extends MineFormRequest
{
    /**
     * 公共规则
     */
    public function commonRules(): array
    {
        return [];
    }

    /**
     * 发私信验证
     */
    public function sendPrivateMessageRules(): array
    {
        return [
            'title' => 'required',
            'users' => 'required|array',
            'content' => 'required'
        ];
    }

    /**
     * 字段映射名称
     * return array
     */
    public function attributes(): array
    {
        return [
            'title' => '信息标题',
            'users' => '接受用户列表',
            'content' => '信息内容',
        ];
    }
}