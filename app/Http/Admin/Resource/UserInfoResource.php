<?php

namespace App\Http\Admin\Resource;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserInfoResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            /** 用户名 */
            'username' => $this->username,
            /** 昵称 */
            'nickname' => $this->nickname,
            /** 头像 */
            'avatar' => $this->avatar,
            /** 个人签名 */
            'signed' => $this->signed,
            /** 后台设置 */
            'backend_setting' => $this->backend_setting,
            /** 手机号 */
            'phone' => $this->phone,
            /** 邮箱 */
            'email' => $this->email,
        ];
    }
}
