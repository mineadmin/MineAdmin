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

namespace App\Schema;

use App\Model\UserLoginLog;
use Hyperf\Swagger\Annotation\Property;
use Hyperf\Swagger\Annotation\Schema;

#[Schema(title: 'UserLoginLogSchema')]
class UserLoginLogSchema implements \JsonSerializable
{
    #[Property(property: 'id', title: '主键', type: 'int')]
    public ?int $id;

    #[Property(property: 'username', title: '用户名', type: 'string')]
    public ?string $username;

    #[Property(property: 'ip', title: '登录IP地址', type: 'string')]
    public ?string $ip;

    #[Property(property: 'ip_location', title: 'IP所属地', type: 'string')]
    public ?string $ipLocation;

    #[Property(property: 'os', title: '操作系统', type: 'string')]
    public ?string $os;

    #[Property(property: 'browser', title: '浏览器', type: 'string')]
    public ?string $browser;

    #[Property(property: 'status', title: '登录状态 (1成功 2失败)', type: 'int')]
    public ?int $status;

    #[Property(property: 'message', title: '提示消息', type: 'string')]
    public ?string $message;

    #[Property(property: 'login_time', title: '登录时间', type: 'mixed')]
    public mixed $loginTime;

    #[Property(property: 'remark', title: '备注', type: 'string')]
    public ?string $remark;

    public function __construct(UserLoginLog $model)
    {
        $this->id = $model->id;
        $this->username = $model->username;
        $this->ip = $model->ip;
        $this->ipLocation = $model->ip_location;
        $this->os = $model->os;
        $this->browser = $model->browser;
        $this->status = $model->status;
        $this->message = $model->message;
        $this->loginTime = $model->login_time;
        $this->remark = $model->remark;
    }

    public function jsonSerialize(): mixed
    {
        return ['id' => $this->id, 'username' => $this->username, 'ip' => $this->ip, 'ip_location' => $this->ipLocation, 'os' => $this->os, 'browser' => $this->browser, 'status' => $this->status, 'message' => $this->message, 'login_time' => $this->loginTime, 'remark' => $this->remark];
    }
}
